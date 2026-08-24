<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests;

use Ailos\Sdk\Entities\Enviroment;
use Ailos\Sdk\Tests\Support\NgrokTunnelResolver;
use Dotenv\Dotenv;
use Dotenv\Repository\RepositoryBuilder;
use PHPUnit\Framework\TestCase;

abstract class IntegrationTestCase extends TestCase
{
    protected static Enviroment $enviroment;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $repository = RepositoryBuilder::createWithDefaultAdapters()->make();

        $dotenv = Dotenv::create($repository, PROJECT_ROOT, '.env');
        $dotenv->safeLoad();

        $publicCallbackBaseUrl = (new NgrokTunnelResolver())->getPublicUrl() . '/callback/jwt';

        self::$enviroment = new Enviroment(
            (string) ($repository->get('AILOS_CONSUMER_KEY') ?? ''),
            (string) ($repository->get('AILOS_CONSUMER_SECRET') ?? ''),
            (string) ($publicCallbackBaseUrl ?? ''),
            (string) ($repository->get('AILOS_API_KEY_DEVELOPER') ?? ''),
            (string) ($repository->get('AILOS_CODIGO_COOPERATIVA') ?? ''),
            (string) ($repository->get('AILOS_CODIGO_CONTA') ?? ''),
            (string) ($repository->get('AILOS_SENHA') ?? ''),
        );

        if (self::$enviroment->ambiente != 'homol') {
            throw new \Exception('Ambiente invalido para testes');
        }
    }
}
