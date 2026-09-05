<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests;

use Ailos\Sdk\Entities\Enviroment;
use Ailos\Sdk\Entities\SdkConfig;
use Dotenv\Dotenv;
use Dotenv\Repository\RepositoryBuilder;
use Dotenv\Repository\RepositoryInterface;
use PHPUnit\Framework\TestCase;

abstract class IntegrationTestCase extends TestCase
{
    protected static Enviroment $enviroment;
    protected static SdkConfig $config;
    private static RepositoryInterface $repository;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$repository = RepositoryBuilder::createWithDefaultAdapters()->make();

        $dotenv = Dotenv::create(self::$repository, PROJECT_ROOT, '.env');
        $dotenv->safeLoad();

        self::$enviroment = new Enviroment(
            self::requiredEnv('AILOS_CONSUMER_KEY'),
            self::requiredEnv('AILOS_CONSUMER_SECRET'),
            self::requiredEnv('AILOS_URL_CALLBACK'),
            self::requiredEnv('AILOS_API_KEY_DEVELOPER'),
            self::requiredEnv('AILOS_CODIGO_COOPERATIVA'),
            self::requiredEnv('AILOS_CODIGO_CONTA'),
            self::requiredEnv('AILOS_SENHA'),
            'homol', // unico ambiente permitido para testes
            self::requiredEnv('CATCHER_URL'),
            self::requiredEnv('CATCHER_SECRET'),
        );

        self::$config = new SdkConfig();
    }

    protected static function env(string $key, ?string $default = null): ?string
    {
        return (string) (self::$repository->get($key) ?? $default);
    }

    protected static function requiredEnv(string $key): string
    {
        $value = self::env($key);

        if ($value === null || $value === '') {
            throw new \RuntimeException("Variável de ambiente obrigatória não definida: {$key}");
        }

        return $value;
    }
}
