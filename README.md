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


---

## Requisitos

- PHP `^8.5`.
- Composer.
- Extensão `curl` habilitada, caso queira utilizar o http-client interno (recomendado).

---

## Instalação

```bash
composer require ailos/sdk
```

---

## Instanciando o SDK

```php
<?php

use Ailos\Sdk\AilosSdk;
use Ailos\Sdk\Collection\Auth\Credentials\ClientCredentials;
use Ailos\Sdk\Collection\Auth\Credentials\CooperadoCredentials;

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
```

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

### Ambientes

O SDK suporta dois ambientes. Por padrão, o ambiente é `homologacao`.

| Ambiente | Descrição | Base URL |
|---|---|---|
| `homologacao` | Testes e desenvolvimento | `https://apiendpointhml.ailos.coop.br` |
| `producao` | Ambiente real | `https://apiendpoint.ailos.coop.br` |

Para alternar entre eles, basta passar o ambiente desejado na instanciação do SDK, no parametro `environment`.

---

## Autenticação

O processo de autenticação da API Ailos é composto por três etapas realizadas automaticamente pelo SDK:

1. **Geração do Access Token** — usando suas credenciais de aplicação (Consumer Key e Consumer Secret)
2. **Obtenção do ID** — usando o Access Token junto com os dados da sua aplicação
3. **Autenticação do Cooperado** — usando o Access Token e o ID para autenticar o cooperado, cujo resultado (JWT) é enviado para a URL de callback configurada

O SDK gerencia todo esse fluxo internamente, basta você chamar:

```php
[$accessToken, $jwt] = $sdk->auth()->authenticate();
```

### Como desenvolver o endpoint de callback

Para que o SDK consiga orquestrar todas as 3 etapas da authenticação é fundamental que você registre uma rota chamando `$sdk->auth()->callbackHandler()`.

O `callbackHandler()` disponibiliza três métodos de entrada para se adaptar a qualquer contexto:

| Método | Quando usar |
|---|---|
| `handleFromGlobals()` | PHP puro — lê automaticamente `php://input` ou `$_POST` |
| `handleFromJson(string $json)` | Frameworks que expõem o corpo como string bruta |
| `handleFromArray(array $data)` | Frameworks que expõem o corpo como array |

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

## Roadmap

### Concluído

| Funcionalidade | Status |
|---|---|
| Fluxo de Autenticação | ![Concluído](https://img.shields.io/badge/Concluído-2da44e?style=flat-square) |

### Em Progresso

| Funcionalidade | Status |
|---|---|
| **API Pagadores** | |
| &nbsp;&nbsp;&nbsp;&nbsp;Cadastrar Pagador | ![Em progresso](https://img.shields.io/badge/Em%20progresso-d4a017?style=flat-square) |
| &nbsp;&nbsp;&nbsp;&nbsp;Alterar Pagador | ![Em progresso](https://img.shields.io/badge/Em%20progresso-d4a017?style=flat-square) |
| &nbsp;&nbsp;&nbsp;&nbsp;Consultar Pagador | ![Em progresso](https://img.shields.io/badge/Em%20progresso-d4a017?style=flat-square) |
| &nbsp;&nbsp;&nbsp;&nbsp;Listar Pagadores | ![Em progresso](https://img.shields.io/badge/Em%20progresso-d4a017?style=flat-square) |
| &nbsp;&nbsp;&nbsp;&nbsp;Totalizar Pagadores | ![Em progresso](https://img.shields.io/badge/Em%20progresso-d4a017?style=flat-square) |
| &nbsp;&nbsp;&nbsp;&nbsp;Exportar Pagadores | ![Em progresso](https://img.shields.io/badge/Em%20progresso-d4a017?style=flat-square) |
| &nbsp;&nbsp;&nbsp;&nbsp;Importar Pagadores | ![Em progresso](https://img.shields.io/badge/Em%20progresso-d4a017?style=flat-square) |

### Planejado

| Funcionalidade                                                              | Status |
|-----------------------------------------------------------------------------|---|
| **Emissão — Boletos**                                                       | |
| &nbsp;&nbsp;&nbsp;&nbsp;Gerar Boleto — Único (V2)                           | ![Planejado](https://img.shields.io/badge/Planejado-8b949e?style=flat-square) |
| &nbsp;&nbsp;&nbsp;&nbsp;Gerar Boleto — Lote (V2)                            | ![Planejado](https://img.shields.io/badge/Planejado-8b949e?style=flat-square) |
| &nbsp;&nbsp;&nbsp;&nbsp;Consultar Boleto — Único (V2)                       | ![Planejado](https://img.shields.io/badge/Planejado-8b949e?style=flat-square) |
| **Emissão — Carnês**                                                        | |
| &nbsp;&nbsp;&nbsp;&nbsp;Gerar Carnê — Único (V1)                            | ![Planejado](https://img.shields.io/badge/Planejado-8b949e?style=flat-square) |
| &nbsp;&nbsp;&nbsp;&nbsp;Gerar Carnê — Lote (V2)                             | ![Planejado](https://img.shields.io/badge/Planejado-8b949e?style=flat-square) |
| &nbsp;&nbsp;&nbsp;&nbsp;Consultar Carnê — Único (V1)                        | ![Planejado](https://img.shields.io/badge/Planejado-8b949e?style=flat-square) |
| &nbsp;&nbsp;&nbsp;&nbsp;Consultar Carnê — Lote (V1)                         | ![Planejado](https://img.shields.io/badge/Planejado-8b949e?style=flat-square) |
| **Utilitários**                                                             | |
| &nbsp;&nbsp;&nbsp;&nbsp;Gerar PDF do Boleto                                 | ![Planejado](https://img.shields.io/badge/Planejado-8b949e?style=flat-square) |
| &nbsp;&nbsp;&nbsp;&nbsp;Gerar PDF de Múltiplos Boletos                      | ![Planejado](https://img.shields.io/badge/Planejado-8b949e?style=flat-square) |
| &nbsp;&nbsp;&nbsp;&nbsp;Validar state do callback                           | ![Planejado](https://img.shields.io/badge/Planejado-8b949e?style=flat-square) |
| &nbsp;&nbsp;&nbsp;&nbsp;Refresh dos Tokens                                  | ![Planejado](https://img.shields.io/badge/Planejado-8b949e?style=flat-square) |
| &nbsp;&nbsp;&nbsp;&nbsp;Listar Movimentação                                 | ![Planejado](https://img.shields.io/badge/Planejado-8b949e?style=flat-square) |
| &nbsp;&nbsp;&nbsp;&nbsp;Utilizar HTML de authenticação fornecido pela Ailos | ![Planejado](https://img.shields.io/badge/Planejado-8b949e?style=flat-square) |

---

## Contribuidores
[![Contribuidores](https://contrib.rocks/image?repo=senna73/ailos-sdk)](https://github.com/senna73/ailos-sdk/graphs/contributors)

---

Licença [MIT](LICENSE) © [Vinícius de Senna](mailto:sennadevinicius@gmail.com)
