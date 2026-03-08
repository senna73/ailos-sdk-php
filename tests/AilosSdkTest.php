<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests;

use Ailos\Sdk\AilosSdk;
use Ailos\Sdk\Auth\Credentials\ClientCredentials;
use Ailos\Sdk\Auth\Credentials\CooperadoCredentials;
use Ailos\Sdk\Auth\Tokens\JwtToken;
use Ailos\Sdk\Exceptions\AuthenticationException;
use Ailos\Sdk\Http\Contracts\HttpClientInterface;
use Ailos\Sdk\Storage\InMemoryTokenStore;
use Ailos\Sdk\Storage\TokenKeys;
use PHPUnit\Framework\TestCase;

class AilosSdkTest extends TestCase
{
    private ClientCredentials    $clientCredentials;
    private CooperadoCredentials $cooperadoCredentials;

    protected function setUp(): void
    {
        $this->clientCredentials = new ClientCredentials('key', 'secret');

        $this->cooperadoCredentials = new CooperadoCredentials(
            urlCallback:          'https://callback.url',
            ailosApiKeyDeveloper: 'uuid-key',
            codigoCooperativa:    '0001',
            codigoConta:          '12345',
            senha:                'senha123',
        );
    }

    public function test_authenticate_runs_full_flow_without_exception(): void
    {
        $httpClient = $this->makeHttpClientForFullFlow();

        $sdk = $this->makeSdk($httpClient);
        $sdk->authenticate();

        // Sem exceção = fluxo executado com sucesso
        $this->expectNotToPerformAssertions();
    }

    public function test_handle_callback_stores_jwt(): void
    {
        $store      = new InMemoryTokenStore();
        $httpClient = $this->makeHttpClientForFullFlow();

        $sdk = $this->makeSdk($httpClient, $store);
        $sdk->authenticate();
        $sdk->handleCallback('my.jwt.token');

        $jwt = $store->get(TokenKeys::JWT);

        $this->assertInstanceOf(JwtToken::class, $jwt);
        $this->assertSame('my.jwt.token', $jwt->value());
    }

    public function test_get_jwt_returns_valid_jwt_after_callback(): void
    {
        $store      = new InMemoryTokenStore();
        $httpClient = $this->makeHttpClientForFullFlow();

        $sdk = $this->makeSdk($httpClient, $store);
        $sdk->authenticate();
        $sdk->handleCallback('my.jwt.token');

        $jwt = $sdk->getJwt();

        $this->assertInstanceOf(JwtToken::class, $jwt);
        $this->assertSame('my.jwt.token', $jwt->value());
    }

    public function test_get_jwt_value_returns_string(): void
    {
        $store      = new InMemoryTokenStore();
        $httpClient = $this->makeHttpClientForFullFlow();

        $sdk = $this->makeSdk($httpClient, $store);
        $sdk->authenticate();
        $sdk->handleCallback('my.jwt.token');

        $this->assertSame('my.jwt.token', $sdk->getJwtValue());
    }

    public function test_get_jwt_throws_when_callback_not_handled(): void
    {
        $this->expectException(AuthenticationException::class);

        $httpClient = $this->makeHttpClientForFullFlow();

        $sdk = $this->makeSdk($httpClient);
        $sdk->authenticate();
        $sdk->getJwt(); // JWT nunca foi armazenado via handleCallback
    }

    public function test_is_authenticated_returns_false_before_authentication(): void
    {
        $sdk = $this->makeSdk($this->createMock(HttpClientInterface::class));

        $this->assertFalse($sdk->isAuthenticated());
    }

    public function test_is_authenticated_returns_true_after_callback(): void
    {
        $store      = new InMemoryTokenStore();
        $httpClient = $this->makeHttpClientForFullFlow();

        $sdk = $this->makeSdk($httpClient, $store);
        $sdk->authenticate();
        $sdk->handleCallback('my.jwt.token');

        $this->assertTrue($sdk->isAuthenticated());
    }

    public function test_logout_clears_all_tokens(): void
    {
        $store      = new InMemoryTokenStore();
        $httpClient = $this->makeHttpClientForFullFlow();

        $sdk = $this->makeSdk($httpClient, $store);
        $sdk->authenticate();
        $sdk->handleCallback('my.jwt.token');

        $this->assertTrue($sdk->isAuthenticated());

        $sdk->logout();

        $this->assertFalse($sdk->isAuthenticated());
    }

    public function test_sdk_accepts_homologacao_environment(): void
    {
        $this->expectNotToPerformAssertions();

        $this->makeSdk(
            httpClient:  $this->createMock(HttpClientInterface::class),
            environment: 'homologacao',
        );
    }

    public function test_sdk_accepts_producao_environment(): void
    {
        $this->expectNotToPerformAssertions();

        $this->makeSdk(
            httpClient:  $this->createMock(HttpClientInterface::class),
            environment: 'producao',
        );
    }

    public function test_sdk_throws_on_invalid_environment(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->makeSdk(
            httpClient:  $this->createMock(HttpClientInterface::class),
            environment: 'invalid',
        );
    }

    // Helpers
    private function makeSdk(
        HttpClientInterface  $httpClient,
        ?InMemoryTokenStore  $store = null,
        string               $environment = 'homologacao',
    ): AilosSdk {
        return new AilosSdk(
            clientCredentials:    $this->clientCredentials,
            cooperadoCredentials: $this->cooperadoCredentials,
            environment:          $environment,
            httpClient:           $httpClient,
            tokenStore:           $store ?? new InMemoryTokenStore(),
        );
    }

    private function makeHttpClientForFullFlow(): HttpClientInterface
    {
        $httpClient = $this->createMock(HttpClientInterface::class);

        // Etapa 1 — Access Token
        $httpClient->method('postUrlEncoded')->willReturn([
            'access_token' => 'access-token-value',
            'expires_in'   => 3600,
            '_status_code' => 200,
        ]);

        // Etapa 2 — Auth ID
        $httpClient->method('post')->willReturn([
            'id'           => 'auth-id-value',
            '_status_code' => 200,
        ]);

        // Etapa 3 — Autenticação do cooperado (JWT via callback)
        $httpClient->method('postForm')->willReturn([
            '_status_code' => 200,
        ]);

        return $httpClient;
    }
}
