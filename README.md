# Ailos SDK
[![Packagist](https://img.shields.io/packagist/v/ailos/sdk)](https://packagist.org/packages/ailos/sdk)
[![PHP Version](https://img.shields.io/badge/php-%5E8.5-blue.svg)](https://www.php.net/)
[![License](https://img.shields.io/github/license/ViniciusDeSenna/ailos-sdk-php)](LICENSE)
[![Maintenance](https://img.shields.io/maintenance/yes/2026)]()
[![Last Commit](https://img.shields.io/github/last-commit/ViniciusDeSenna/ailos-sdk-php)](https://github.com/ViniciusDeSenna/ailos-sdk-php/commits)
[![Issues](https://img.shields.io/github/issues/ViniciusDeSenna/ailos-sdk-php)](https://github.com/ViniciusDeSenna/ailos-sdk-php/issues)
[![Code Style](https://img.shields.io/badge/code_style-PSR--12-blue)](https://www.php-fig.org/psr/psr-12/)
![GitHub forks](https://img.shields.io/github/forks/ViniciusDeSenna/ailos-sdk-php?style=social)
[![GitHub Stars](https://img.shields.io/github/stars/ViniciusDeSenna/ailos-sdk-php?style=social)](https://github.com/ViniciusDeSenna/ailos-sdk-php/stargazers)

---

<img alt="Ailos SDK PHP" src="https://github.com/user-attachments/assets/de553609-4521-43e4-8f74-2efdfff8ecd7" />

Este SDK foi desenvolvido para facilitar a integração com os serviços da **Cooperativa Ailos**, oferecendo uma interface simples, segura e eficiente para desenvolvedores PHP.

---

## Requisitos

- PHP `^8.5`
- Extensão `curl` habilitada

---

## Instalação

```bash
composer require ailos/sdk
```

---

## Ambientes

O SDK suporta dois ambientes. Por padrão, o ambiente é `homologacao`.

| Ambiente | Descrição | Base URL |
|---|---|---|
| `homologacao` | Testes e desenvolvimento | `https://apiendpointhml.ailos.coop.br` |
| `producao` | Ambiente real | `https://apiendpoint.ailos.coop.br` |

Para alternar entre eles, basta passar o ambiente desejado na instanciação do SDK:

```php
// Homologação (padrão)
$sdk = new \Ailos\Sdk\AilosSdk(
    clientCredentials:    $clientCredentials,
    cooperadoCredentials: $cooperadoCredentials,
    environment:          'homologacao',
);

// Produção
$sdk = new \Ailos\Sdk\AilosSdk(
    clientCredentials:    $clientCredentials,
    cooperadoCredentials: $cooperadoCredentials,
    environment:          'producao',
);
```

---

## Autenticação

O processo de autenticação da API Ailos é composto por três etapas realizadas automaticamente pelo SDK:

1. **Geração do Access Token** — usando suas credenciais de aplicação (Consumer Key e Consumer Secret)
2. **Obtenção do ID** — usando o Access Token junto com os dados da sua aplicação
3. **Autenticação do Cooperado** — usando o Access Token e o ID para autenticar o cooperado, cujo resultado (JWT) é enviado para a URL de callback configurada

O SDK gerencia todo esse fluxo internamente, incluindo a renovação automática dos tokens antes de expirarem.

### Credenciais necessárias

Para utilizar o SDK você precisará de:

| Credencial | Descrição |
|---|---|
| `consumerKey` | Chave de identificação da sua aplicação |
| `consumerSecret` | Segredo da sua aplicação |
| `urlCallback` | URL do seu endpoint que receberá o JWT |
| `ailosApiKeyDeveloper` | UUID do desenvolvedor fornecido pela Ailos |
| `codigoCooperativa` | Código da cooperativa do cooperado |
| `codigoConta` | Código da conta do cooperado |
| `senha` | Senha de acesso do cooperado |

### Instanciando o SDK

```php
<?php

use Ailos\Sdk\AilosSdk;
use Ailos\Sdk\Auth\Credentials\ClientCredentials;
use Ailos\Sdk\Auth\Credentials\CooperadoCredentials;

$clientCredentials = new ClientCredentials(
    consumerKey:    'sua-consumer-key',
    consumerSecret: 'seu-consumer-secret',
);

$cooperadoCredentials = new CooperadoCredentials(
    urlCallback:          'https://sua-aplicacao.com.br/ailos/callback',
    ailosApiKeyDeveloper: 'seu-uuid-developer',
    codigoCooperativa:    '0001',
    codigoConta:          '123456',
    senha:                'senha-do-cooperado',
);

$sdk = new AilosSdk(
    clientCredentials:    $clientCredentials,
    cooperadoCredentials: $cooperadoCredentials,
    environment:          'homologacao',
);

// Inicia o fluxo de autenticação
// O JWT será enviado pela Ailos para a sua urlCallback
$sdk->authenticate();
```

Após chamar `authenticate()`, a Ailos enviará um `POST` para a `urlCallback` configurada com o seguinte payload JSON:

```json
{
    "state": "identificador-da-chamada",
    "code": "eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9..."
}
```

O campo `code` contém o JWT que deve ser informado ao SDK para que a autenticação seja concluída. O SDK oferece **duas abordagens** para isso — escolha a que melhor se encaixa no seu contexto.

---

## Abordagens para o endpoint de callback

### Abordagem 1 — Automática com `callbackHandler()` ✅ Recomendada

O SDK processa o callback automaticamente. Você apenas registra uma rota apontando para o handler — sem precisar entender o payload ou extrair campos manualmente.

**PHP puro:**

```php
// POST /ailos/callback
$sdk->callbackHandler()->handleFromGlobals();
```

**Laravel:**

```php
// routes/api.php
use Illuminate\Http\Request;

Route::post('/ailos/callback', function (Request $request) {
    $sdk->callbackHandler()->handleFromArray($request->all());

    return response()->json(['message' => 'Autenticação concluída.']);
});
```

**Symfony:**

```php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ailos/callback', methods: ['POST'])]
public function callback(Request $request): JsonResponse
{
    $sdk->callbackHandler()->handleFromJson($request->getContent());

    return $this->json(['message' => 'Autenticação concluída.']);
}
```

**Slim:**

```php
$app->post('/ailos/callback', function (Request $request, Response $response) use ($sdk) {
    $sdk->callbackHandler()->handleFromArray((array) $request->getParsedBody());

    $response->getBody()->write(json_encode(['message' => 'Autenticação concluída.']));
    return $response->withHeader('Content-Type', 'application/json');
});
```

O `callbackHandler()` disponibiliza três métodos de entrada para se adaptar a qualquer contexto:

| Método | Quando usar |
|---|---|
| `handleFromGlobals()` | PHP puro — lê automaticamente `php://input` ou `$_POST` |
| `handleFromJson(string $json)` | Frameworks que expõem o corpo como string bruta |
| `handleFromArray(array $data)` | Frameworks que expõem o corpo como array |

Após o processamento, todos os métodos retornam um `CallbackPayload` com acesso ao `state` — útil para correlacionar o callback com a sessão que o originou:

```php
$payload = $sdk->callbackHandler()->handleFromArray($request->all());

$state = $payload->state(); // "identificador-da-chamada"
```

---

### Abordagem 2 — Manual com `handleCallback()` 🔧 Controle total

Para casos onde você precisa processar o payload manualmente antes de informar o JWT ao SDK — por exemplo, validar o `state`, registrar logs ou aplicar lógica customizada.

```php
// POST /ailos/callback

// 1. Leia e processe o payload da forma que preferir
$body  = json_decode(file_get_contents('php://input'), associative: true);
$jwt   = $body['code']  ?? '';
$state = $body['state'] ?? '';

// 2. Lógica customizada antes de informar o JWT ao SDK
if ($state !== $_SESSION['ailos_state_esperado']) {
    http_response_code(422);
    echo json_encode(['error' => 'State inválido.']);
    exit;
}

// 3. Informe o JWT ao SDK manualmente
$sdk->handleCallback($jwt);
```

**Quando preferir a Abordagem 2:**
- Você precisa validar o `state` antes de aceitar o JWT
- Você quer registrar logs detalhados do payload recebido
- Você precisa de lógica de negócio customizada no callback
- Você está integrando com um sistema de autenticação próprio

---

## Utilizando o JWT nas chamadas à API

Independente da abordagem escolhida para o callback, o uso do JWT é idêntico. O SDK renova o token automaticamente quando necessário:

```php
if ($sdk->isAuthenticated()) {
    // Retorna o JWT como string — renova automaticamente se necessário
    $jwt = $sdk->getJwtValue();

    // Use o JWT no header das suas requisições à API Ailos
    $headers = [
        'x-ailos-authentication: ' . $jwt,
    ];
}
```

### Encerrando a sessão

```php
$sdk->logout();
```

---

## Tratamento de erros

Todas as exceções do SDK herdam de `AilosSdkException`, permitindo capturar qualquer erro com um único `catch`. Para tratamentos mais específicos, utilize as exceções tipadas:

```php
use Ailos\Sdk\Exceptions\AilosSdkException;
use Ailos\Sdk\Exceptions\AuthenticationException;
use Ailos\Sdk\Exceptions\InvalidCredentialsException;
use Ailos\Sdk\Exceptions\TokenExpiredException;
use Ailos\Sdk\Exceptions\HttpException;

try {
    $sdk->authenticate();

} catch (InvalidCredentialsException $e) {
    // Consumer Key, Consumer Secret ou dados do cooperado inválidos
    echo 'Credenciais inválidas: ' . $e->getMessage();

} catch (TokenExpiredException $e) {
    // Token expirou e não pode ser renovado — novo login necessário
    echo 'Token expirado: ' . $e->getMessage();

} catch (AuthenticationException $e) {
    // Falha genérica no fluxo de autenticação
    echo 'Erro de autenticação: ' . $e->getMessage();

} catch (HttpException $e) {
    // Falha na camada HTTP (timeout, erro 5xx, etc.)
    echo 'Erro HTTP ' . $e->getStatusCode() . ': ' . $e->getMessage();

} catch (AilosSdkException $e) {
    // Qualquer outro erro do SDK
    echo 'Erro no SDK: ' . $e->getMessage();
}
```

As exceções do `callbackHandler()` seguem o mesmo padrão:

```php
try {
    $sdk->callbackHandler()->handleFromArray($request->all());

} catch (AuthenticationException $e) {
    // Payload inválido ou campo 'code' ausente
    echo 'Erro no callback: ' . $e->getMessage();
}
```

### Hierarquia de exceções

```
AilosSdkException
├── HttpException                   → falhas de transporte HTTP
└── AuthenticationException         → falhas no fluxo de autenticação
    ├── InvalidCredentialsException → credenciais inválidas
    └── TokenExpiredException       → token expirado sem possibilidade de renovação
```

---

## Contribuindo

Contribuições são bem-vindas! Para contribuir:

1. Faça um fork do repositório
2. Crie uma branch para sua feature: `git checkout -b feat/minha-feature`
3. Commite suas alterações seguindo [Conventional Commits](https://www.conventionalcommits.org/pt-br/)
4. Abra um Pull Request descrevendo as mudanças

Por favor, certifique-se de que os testes passam e o code style está correto antes de abrir o PR:

```bash
composer quality
```

---

## Licença

[MIT](LICENSE) © [Vinícius de Senna](mailto:sennadevinicius@gmail.com)

---

## Contribuidores
[![Contribuidores](https://contrib.rocks/image?repo=senna73/ailos-sdk)](https://github.com/senna73/ailos-sdk/graphs/contributors)
