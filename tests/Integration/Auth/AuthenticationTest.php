<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests\Integration\Auth;

use Ailos\Sdk\Auth\Tokens\AccessToken;
use Ailos\Sdk\Exceptions\AilosSdkException;
use Ailos\Sdk\Tests\Integration\IntegrationTestCase;

class AuthenticationTest extends IntegrationTestCase
{
    public function test_fetches_access_token_from_real_api(): void
    {
        $accessToken = $this->sdk
            ->getOrchestrator()
            ->resolveAccessToken($this->sdk->getClientCredentials());

        $this->assertInstanceOf(AccessToken::class, $accessToken);
        $this->assertNotEmpty($accessToken->value());
        $this->assertFalse($accessToken->isExpired());

        echo "\n✅ Access Token obtido: " . substr($accessToken->value(), 0, 20) . '...';
    }

    public function test_full_authentication_flow_reaches_callback_step(): void
    {
        // O fluxo completo dispara as 3 etapas.
        // O JWT chega via callback — aqui apenas confirmamos que
        // as etapas 1 e 2 funcionam sem lançar exceções.
        try {
            $this->sdk->authenticate();
            $this->assertTrue(true); // Etapas 1 e 2 concluídas com sucesso
            echo "\n✅ Etapas 1 e 2 concluídas. JWT será enviado para: " . $_ENV['AILOS_URL_CALLBACK'];
        } catch (AilosSdkException $e) {
            $this->fail('Falha no fluxo de autenticação: ' . $e->getMessage());
        }
    }

    public function test_callback_handler_stores_jwt_correctly(): void
    {
        // Simula o recebimento do JWT via callback após authenticate()
        // Use um JWT real de homologação obtido manualmente para este teste
        $jwtReal = $_ENV['AILOS_JWT_TEST'] ?? null;

        if (empty($jwtReal)) {
            $this->markTestSkipped(
                'Variável AILOS_JWT_TEST não definida no .env. ' .
                'Adicione um JWT válido de homologação para testar o callback handler.'
            );
        }

        $this->sdk->handleCallback($jwtReal);

        $this->assertTrue($this->sdk->isAuthenticated());
        $this->assertNotEmpty($this->sdk->getJwtValue());

        echo "\n✅ JWT armazenado via handleCallback com sucesso.";
    }

    public function test_callback_handler_processes_json_payload(): void
    {
        $jwtReal = $_ENV['AILOS_JWT_TEST'] ?? null;

        if (empty($jwtReal)) {
            $this->markTestSkipped(
                'Variável AILOS_JWT_TEST não definida no .env.'
            );
        }

        $payload = json_encode([
            'code'  => $jwtReal,
            'state' => 'integration-test-state',
        ]);

        $result = $this->sdk->callbackHandler()->handleFromJson($payload);

        $this->assertSame('integration-test-state', $result->state());
        $this->assertTrue($this->sdk->isAuthenticated());

        echo "\n✅ callbackHandler processou o payload JSON corretamente.";
    }

    public function test_sdk_is_not_authenticated_before_callback(): void
    {
        $this->assertFalse($this->sdk->isAuthenticated());
    }

    public function test_logout_clears_authenticated_state(): void
    {
        $jwtReal = $_ENV['AILOS_JWT_TEST'] ?? null;

        if (empty($jwtReal)) {
            $this->markTestSkipped(
                'Variável AILOS_JWT_TEST não definida no .env.'
            );
        }

        $this->sdk->handleCallback($jwtReal);
        $this->assertTrue($this->sdk->isAuthenticated());

        $this->sdk->logout();

        $this->assertFalse($this->sdk->isAuthenticated());
        echo "\n✅ logout() limpou o estado de autenticação corretamente.";
    }
}
