<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests\Integration;

use Ailos\Sdk\Framework\AuthManager;
use Ailos\Sdk\Framework\HttpClient;
use Ailos\Sdk\Framework\Storage\FileStorage;
use Ailos\Sdk\Tests\IntegrationTestCase;

class AuthManagerTest extends IntegrationTestCase
{
    public function testAuthManager(): void
    {
        $authManager = new AuthManager(parent::$enviroment, new HttpClient(), new FileStorage());

        $authManager->auth();

        $authManager->logout();
    }
}
