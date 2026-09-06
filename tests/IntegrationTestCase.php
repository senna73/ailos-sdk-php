<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests;

use Ailos\Sdk\Config\AilosContext;
use Ailos\Sdk\Config\Env;
use Ailos\Sdk\Config\Environment;
use Ailos\Sdk\Config\SdkConfig;
use PHPUnit\Framework\TestCase;

abstract class IntegrationTestCase extends TestCase
{
    public static AilosContext $context;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $environment = new Environment(
            Env::requiredEnv('AILOS_CONSUMER_KEY'),
            Env::requiredEnv('AILOS_CONSUMER_SECRET'),
            Env::requiredEnv('AILOS_URL_CALLBACK'),
            Env::requiredEnv('AILOS_API_KEY_DEVELOPER'),
            Env::requiredEnv('AILOS_CODIGO_COOPERATIVA'),
            Env::requiredEnv('AILOS_CODIGO_CONTA'),
            Env::requiredEnv('AILOS_SENHA'),
            'homol'
        );

        $config = new SdkConfig(
            catcherService: true,
            catcherUrl: Env::requiredEnv('CATCHER_URL'),
            catcherSecret: Env::requiredEnv('CATCHER_SECRET'),
        );

        self::$context = new AilosContext($environment, $config);
    }
}
