# Catcher

Serviço auxiliar para testes de integração do SDK contra o ambiente de homologação da Ailos.

## Por que isso existe

A Ailos, como a maioria das instituições financeiras, **não aceita URLs de túnel** (ngrok, localtunnel, etc.) como Callback URL em fluxos de autorização — esses domínios costumam estar bloqueados por política de segurança/compliance.

Como os testes de integração deste SDK precisam validar o fluxo real de callback (a Ailos envia um `code`/JWT de forma assíncrona para uma URL pública), foi necessário um serviço com **domínio próprio e HTTPS válido**, sempre disponível, para receber esse callback. É isso que o catcher faz.

## Como funciona

O catcher é intencionalmente burro: ele **não interpreta, não valida e não transforma** o payload que recebe. Ele só guarda e devolve, exatamente como chegou.

```
┌──────────────────────────────┐
│        Ailos (Homolog)       │
└──────────────┬───────────────┘
               │
               │ POST /callback
               │
               │ {
               │   "state": "...",
               │   "code": "..."
               │ }
               ▼
┌──────────────────────────────────────────────┐
│                  CATCHER                     │
│                                              │
│  Recebe o callback                           │
│       │                                      │
│       ▼                                      │
│  ┌────────────────────────────────────────┐  │
│  │          Memória (APCu)                │  │
│  │                                        │  │
│  │  state ──► payload bruto               │  │
│  │           { code, state, ... }         │  │
│  └────────────────────────────────────────┘  │
│                                              │
│  Mantém o payload associado ao "state"       │
└──────────────────────┬───────────────────────┘
                       │
                       │ GET /events?state=...
                       │ X-Catcher-Secret: ****
                       ▼
┌──────────────────────────────────────────────┐
│              Seus testes                     │
│           Local / CI / Automação             │
│                                              │
│  Recebe o payload bruto, exatamente como     │
│  foi enviado pela Ailos.                     │
└──────────────────────────────────────────────┘
```

O `state` é o mecanismo padrão do fluxo OAuth2 usado para correlacionar a resposta assíncrona com a requisição que a originou. Cada execução de teste gera um `state` (correlation id) único, envia esse valor para a Ailos no início da autorização, e usa o mesmo valor para consultar o catcher depois.

**Importante:** a leitura em `/events` consome o dado (apaga após a primeira leitura bem-sucedida). Cada `state` só pode ser consultado uma vez.

## Endpoints

### `POST /callback`

Chamado pela Ailos. Recebe o corpo enviado por eles e guarda associado ao campo `state` do próprio corpo.

- Sem autenticação (é a Ailos quem chama, não temos como exigir um secret deles).
- Corpo esperado: JSON contendo pelo menos o campo `state`.
- Retorna `200 {"status":"ok"}` em caso de sucesso, `422` se o corpo não tiver `state`.

### `GET /events?state=<valor>`

Chamado pelos seus testes, para consultar se o callback já chegou.

- Requer header `X-Catcher-Secret: <valor do CATCHER_SECRET>`.
- Retorna `200` com o corpo bruto exatamente como a Ailos enviou, `403` se o secret estiver incorreto, `404` se ainda não chegou nenhum callback para aquele `state` (ou se já foi consumido).

## Deploy

O catcher é um serviço Docker independente, pensado para rodar em qualquer VPS/EC2 com um domínio próprio apontando para ela.

### Pré-requisitos na máquina de destino

- Docker + Docker Compose (plugin) instalados.
- Domínio (ou subdomínio) com registro DNS tipo `A` apontando para o IP público da máquina.
- Portas `80` e `443` liberadas no firewall/security group — obrigatórias para o Caddy emitir e renovar o certificado HTTPS automaticamente (Let's Encrypt).
- Se o DNS estiver atrás de um proxy (ex: Cloudflare), o registro precisa estar em modo **"DNS only"** durante a subida inicial do container — proxies na frente podem quebrar a validação do certificado. Após o container subir e o Caddy emitir o certificado com sucesso (confirme nos logs, veja abaixo), o proxy pode ser reativado normalmente. Se o certificado precisar ser renovado ou reemitido no futuro (ex: volume `caddy_data` perdido), o proxy precisa ser desativado novamente até a nova emissão ser concluída.

### Subindo o serviço

```bash
cd catcher
cp .env.example .env
nano .env   # preencha CATCHER_DOMAIN e CATCHER_SECRET
docker compose -f docker-compose.yml up -d --build
```

Acompanhe a emissão do certificado:

```bash
docker compose -f docker-compose.yml logs -f caddy
```

Procure pela linha `certificate obtained successfully` confirmando que o HTTPS está funcionando.

### Atualizando após mudanças no código

O código é copiado para dentro da imagem no momento do build (não é montado como volume) — isso é proposital, para manter o deploy imutável e rastreável. Isso significa que **qualquer alteração no código exige rebuild**, reiniciar o container sozinho não é suficiente:

```bash
git pull
docker compose -f docker-compose.yml up -d --build catcher
```

## Variáveis de ambiente (`.env` do catcher)

| Variável | Descrição |
|---|---|
| `CATCHER_DOMAIN` | Domínio público do serviço (ex: `catcher.seudominio.com`). Usado pelo Caddy para emitir o certificado HTTPS. |
| `CATCHER_SECRET` | Segredo compartilhado que protege o endpoint `GET /events`. Gere um valor forte, ex: `openssl rand -hex 32`. |

## Integração com o SDK principal

Os testes de integração do SDK (fora desta pasta) também precisam conhecer o `CATCHER_SECRET`, para poder consultar o catcher.

No `.env` **do SDK** (raiz do projeto, não o `.env` desta pasta), adicione:

```
CATCHER_SECRET=<o mesmo valor configurado no .env do catcher, na VPS>
```

O `IntegrationTestCase` usa essa variável para fzer polling em `CATCHER_URL/events?state=<...>`, autenticando com `X-Catcher-Secret: CATCHER_SECRET`, até o callback chegar ou o timeout expirar.

## Rodando localmente para desenvolvimento do próprio catcher

Se você só precisa alterar/testar a lógica do catcher em si (sem publicar nada), pode subir localmente sem domínio nem HTTPS:

```bash
docker build -t catcher-dev .
docker run --rm -p 8080:80 -e CATCHER_SECRET=dev_secret catcher-dev
```

Isso **não** substitui o serviço publicado — a Ailos não consegue chamar `localhost`. Serve apenas para validar a lógica (roteamento, armazenamento no APCu, autenticação) antes de subir uma mudança para a VPS.

## Limitações conhecidas

- O armazenamento é em memória (APCu), local ao processo do container. Reiniciar o container limpa qualquer dado pendente.
- Não há alta disponibilidade — é um serviço único, pensado para uso em testes, não para produção.
- O dado é retido por tempo limitado (ver `Store.php`) e consumido na primeira leitura; não serve como histórico ou auditoria de callbacks.