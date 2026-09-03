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

        $authManager->auth(true);

        $this->assertNotNull($authManager->getAccessToken());
        $this->assertNotNull($authManager->getId());
        $this->assertNotNull($authManager->getState());
        $this->assertNotNull($authManager->getJwt());

        $this->assertInstanceOf(\Ailos\Sdk\Entities\AccessToken::class, $authManager->getAccessToken());
        $this->assertInstanceOf(\Ailos\Sdk\Entities\Jwt::class, $authManager->getJwt());

        $authManager->logout();

        $this->assertNull($authManager->getAccessToken());
        $this->assertNull($authManager->getId());
        $this->assertNull($authManager->getState());
        $this->assertNull($authManager->getJwt());
    }
}
