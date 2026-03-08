<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests\Auth\Steps;

use Ailos\Sdk\Auth\Credentials\ClientCredentials;
use Ailos\Sdk\Auth\Credentials\CooperadoCredentials;
use Ailos\Sdk\Auth\Steps\AuthenticateCooperadoStep;
use Ailos\Sdk\Auth\Steps\FetchAccessTokenStep;
use Ailos\Sdk\Auth\Steps\FetchAuthIdStep;
use Ailos\Sdk\Auth\Tokens\AccessToken;
use Ailos\Sdk\Auth\Tokens\AuthId;
use Ailos\Sdk\Exceptions\AuthenticationException;
use Ailos\Sdk\Exceptions\InvalidCredentialsException;
use Ailos\Sdk\Http\Contracts\HttpClientInterface;
use Ailos\Sdk\Http\Environment;
use PHPUnit\Framework\TestCase;

class StepsTest extends TestCase
{
    private Environment $environment;

    protected function setUp(): void
    {
        $this->environment = new Environment('homologacao');
    }

    // FetchAccessTokenStep
    public function test_fetch_access_token_returns_access_token_on_success(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('postUrlEncoded')->willReturn([
            'access_token' => 'token-abc',
            'expires_in'   => 3600,
            '_status_code' => 200,
        ]);

        $step   = new FetchAccessTokenStep($httpClient, $this->environment);
        $result = $step->execute(new ClientCredentials('key', 'secret'));

        $this->assertInstanceOf(AccessToken::class, $result);
        $this->assertSame('token-abc', $result->value());
    }

    public function test_fetch_access_token_throws_on_401(): void
    {
        $this->expectException(InvalidCredentialsException::class);

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('postUrlEncoded')->willReturn([
            '_status_code' => 401,
        ]);

        $step = new FetchAccessTokenStep($httpClient, $this->environment);
        $step->execute(new ClientCredentials('key', 'secret'));
    }

    public function test_fetch_access_token_throws_when_token_missing_in_response(): void
    {
        $this->expectException(AuthenticationException::class);

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('postUrlEncoded')->willReturn([
            '_status_code' => 200,
        ]);

        $step = new FetchAccessTokenStep($httpClient, $this->environment);
        $step->execute(new ClientCredentials('key', 'secret'));
    }

    public function test_fetch_access_token_throws_on_http_failure(): void
    {
        $this->expectException(AuthenticationException::class);

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('postUrlEncoded')->willThrowException(
            new \RuntimeException('Connection refused')
        );

        $step = new FetchAccessTokenStep($httpClient, $this->environment);
        $step->execute(new ClientCredentials('key', 'secret'));
    }

    // FetchAuthIdStep
    public function test_fetch_auth_id_returns_auth_id_from_id_field(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('post')->willReturn([
            'id'           => 'auth-id-123',
            '_status_code' => 200,
        ]);

        $step   = new FetchAuthIdStep($httpClient, $this->environment);
        $result = $step->execute($this->makeAccessToken(), $this->makeCooperadoCredentials());

        $this->assertInstanceOf(AuthId::class, $result);
        $this->assertSame('auth-id-123', $result->value());
    }

    public function test_fetch_auth_id_returns_auth_id_from_raw_field(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('post')->willReturn([
            'raw'          => 'raw-id-456',
            '_status_code' => 200,
        ]);

        $step   = new FetchAuthIdStep($httpClient, $this->environment);
        $result = $step->execute($this->makeAccessToken(), $this->makeCooperadoCredentials());

        $this->assertSame('raw-id-456', $result->value());
    }

    public function test_fetch_auth_id_throws_when_id_is_missing(): void
    {
        $this->expectException(AuthenticationException::class);

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('post')->willReturn(['_status_code' => 200]);

        $step = new FetchAuthIdStep($httpClient, $this->environment);
        $step->execute($this->makeAccessToken(), $this->makeCooperadoCredentials());
    }

    // AuthenticateCooperadoStep
    public function test_authenticate_cooperado_succeeds_on_200(): void
    {
        $this->expectNotToPerformAssertions();

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('postForm')->willReturn(['_status_code' => 200]);

        $step = new AuthenticateCooperadoStep($httpClient, $this->environment);
        $step->execute($this->makeAccessToken(), new AuthId('id-123'), $this->makeCooperadoCredentials());
    }

    public function test_authenticate_cooperado_throws_on_401(): void
    {
        $this->expectException(InvalidCredentialsException::class);

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('postForm')->willReturn(['_status_code' => 401]);

        $step = new AuthenticateCooperadoStep($httpClient, $this->environment);
        $step->execute($this->makeAccessToken(), new AuthId('id-123'), $this->makeCooperadoCredentials());
    }

    public function test_authenticate_cooperado_throws_on_unexpected_error(): void
    {
        $this->expectException(AuthenticationException::class);

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('postForm')->willReturn(['_status_code' => 500]);

        $step = new AuthenticateCooperadoStep($httpClient, $this->environment);
        $step->execute($this->makeAccessToken(), new AuthId('id-123'), $this->makeCooperadoCredentials());
    }

    // Helpers
    private function makeAccessToken(): AccessToken
    {
        return new AccessToken('access-token-value', 3600);
    }

    private function makeCooperadoCredentials(): CooperadoCredentials
    {
        return new CooperadoCredentials(
            urlCallback: 'https://callback.url',
            ailosApiKeyDeveloper: 'uuid-key',
            codigoCooperativa: '0001',
            codigoConta: '12345',
            senha: 'senha123',
        );
    }
}
