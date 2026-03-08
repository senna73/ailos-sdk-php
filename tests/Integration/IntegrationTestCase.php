<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests\Integration;

use Ailos\Sdk\AilosSdk;
use Ailos\Sdk\Auth\Credentials\ClientCredentials;
use Ailos\Sdk\Auth\Credentials\CooperadoCredentials;
use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

abstract class IntegrationTestCase extends TestCase
{
    protected AilosSdk $sdk;

    protected function setUp(): void
    {
        $this->loadEnvironment();
        $this->assertCredentialsPresent();
        $this->sdk = $this->buildSdk();
    }

    private function loadEnvironment(): void
    {
        $root = dirname(__DIR__, 2);

        if (!file_exists($root . '/.env')) {
            $this->markTestSkipped(
                'Arquivo .env não encontrado. ' .
                'Copie .env.example para .env e preencha com suas credenciais para rodar os testes de integração.'
            );
        }

        $dotenv = Dotenv::createImmutable($root);
        $dotenv->load();
    }

    private function assertCredentialsPresent(): void
    {
        $required = [
            'AILOS_CONSUMER_KEY',
            'AILOS_CONSUMER_SECRET',
            'AILOS_URL_CALLBACK',
            'AILOS_API_KEY_DEVELOPER',
            'AILOS_CODIGO_COOPERATIVA',
            'AILOS_CODIGO_CONTA',
            'AILOS_SENHA',
        ];

        $missing = array_filter(
            $required,
            fn (string $key) => empty($_ENV[$key])
        );

        if (!empty($missing)) {
            $this->markTestSkipped(
                'Credenciais ausentes no .env: ' . implode(', ', $missing) . '. ' .
                'Preencha todas as variáveis obrigatórias para rodar os testes de integração.'
            );
        }
    }

    private function buildSdk(): AilosSdk
    {
        return new AilosSdk(
            clientCredentials: new ClientCredentials(
                consumerKey:    $_ENV['AILOS_CONSUMER_KEY'],
                consumerSecret: $_ENV['AILOS_CONSUMER_SECRET'],
            ),
            cooperadoCredentials: new CooperadoCredentials(
                urlCallback:          $_ENV['AILOS_URL_CALLBACK'],
                ailosApiKeyDeveloper: $_ENV['AILOS_API_KEY_DEVELOPER'],
                codigoCooperativa:    $_ENV['AILOS_CODIGO_COOPERATIVA'],
                codigoConta:          $_ENV['AILOS_CODIGO_CONTA'],
                senha:                $_ENV['AILOS_SENHA'],
            ),
            environment: $_ENV['AILOS_ENVIRONMENT'] ?? 'homologacao',
        );
    }
}
