<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests;

use Ailos\Sdk\Cobranca\Context\CobrancaContext;
use Ailos\Sdk\Env\Env;
use Ailos\Sdk\Http\CurlHttp;
use Ailos\Sdk\Storage\Storage;
use PHPUnit\Framework\TestCase;

abstract class CobrancaTestCase extends TestCase
{
    public static CobrancaContext $context;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$context = new CobrancaContext(
            Env::requiredEnv('AILOS_CONSUMER_KEY'),
            Env::requiredEnv('AILOS_CONSUMER_SECRET'),
            Env::requiredEnv('AILOS_URL_CALLBACK'),
            Env::requiredEnv('AILOS_API_KEY_DEVELOPER'),
            Env::requiredEnv('AILOS_CODIGO_COOPERATIVA'),
            Env::requiredEnv('AILOS_CODIGO_CONTA'),
            Env::requiredEnv('AILOS_SENHA'),
            'homol',
            new Storage(),
            new CurlHttp(),
            true,
            Env::requiredEnv('CATCHER_URL'),
            Env::requiredEnv('CATCHER_SECRET')
        );
    }
}
