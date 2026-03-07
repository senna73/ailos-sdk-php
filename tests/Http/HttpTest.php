<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests\Http;

use Ailos\Sdk\Exceptions\HttpException;
use Ailos\Sdk\Http\AilosHttpClient;
use Ailos\Sdk\Http\Environment;
use PHPUnit\Framework\TestCase;

class HttpTest extends TestCase
{
    // Environment
    public function test_environment_defaults_to_homologacao(): void
    {
        $env = new Environment();

        $this->assertStringContainsString('hml', $env->baseUrl());
    }

    public function test_environment_producao_returns_correct_base_url(): void
    {
        $env = new Environment('producao');

        $this->assertStringNotContainsString('hml', $env->baseUrl());
    }

    public function test_environment_throws_on_invalid_value(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Environment('staging');
    }

    public function test_environment_token_url(): void
    {
        $env = new Environment('homologacao');

        $this->assertStringEndsWith('/token', $env->tokenUrl());
    }

    public function test_environment_auth_id_url(): void
    {
        $env = new Environment('homologacao');

        $this->assertStringContainsString('obter/id', $env->authIdUrl());
    }

    public function test_environment_login_url_contains_encoded_id(): void
    {
        $env = new Environment('homologacao');

        $url = $env->loginUrl('encoded-id-123');

        $this->assertStringContainsString('encoded-id-123', $url);
        $this->assertStringContainsString('login/index', $url);
    }

    public function test_environment_refresh_url_contains_encoded_jwt(): void
    {
        $env = new Environment('homologacao');

        $url = $env->refreshUrl('encoded-jwt-abc');

        $this->assertStringContainsString('encoded-jwt-abc', $url);
        $this->assertStringContainsString('token/refresh', $url);
    }

    public function test_environment_is_production_returns_false_for_homologacao(): void
    {
        $env = new Environment('homologacao');

        $this->assertFalse($env->isProduction());
    }

    public function test_environment_is_production_returns_true_for_producao(): void
    {
        $env = new Environment('producao');

        $this->assertTrue($env->isProduction());
    }

    // AilosHttpClient — testa apenas comportamento sem fazer requisições reais
    public function test_http_client_implements_interface(): void
    {
        $client = new AilosHttpClient();

        $this->assertInstanceOf(\Ailos\Sdk\Http\Contracts\HttpClientInterface::class, $client);
    }

    public function test_http_exception_is_thrown_on_bad_status(): void
    {
        $exception = HttpException::fromResponse(401, 'Unauthorized');

        $this->assertSame(401, $exception->getStatusCode());
        $this->assertStringContainsString('401', $exception->getMessage());
    }

    public function test_http_exception_is_thrown_on_connection_failure(): void
    {
        $exception = HttpException::fromConnectionFailure('Connection refused');

        $this->assertSame(0, $exception->getStatusCode());
        $this->assertStringContainsString('Connection refused', $exception->getMessage());
    }
}