<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests\Integration;

use Ailos\Sdk\Entities\AccessToken;
use Ailos\Sdk\Framework\AuthManager;
use Ailos\Sdk\Framework\HttpClient;
use Ailos\Sdk\Tests\IntegrationTestCase;

class AuthManagerTest extends IntegrationTestCase
{
    public function testAuthManager(): void
    {
        $authManager = new AuthManager(parent::$enviroment, new HttpClient());

        $authManager->auth();

        $this->assertInstanceOf(AccessToken::class, $authManager->accessToken);
        $this->assertNotEmpty($authManager->accessToken->accessToken);
        $this->assertNotEmpty($authManager->id);

        $authManager->logout();
    }
}
