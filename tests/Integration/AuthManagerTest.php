<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests\Integration;

use Ailos\Sdk\Entities\AccessToken;
use Ailos\Sdk\Entities\Jwt;
use Ailos\Sdk\Framework\AuthManager;
use Ailos\Sdk\Tests\IntegrationTestCase;

class AuthManagerTest extends IntegrationTestCase
{
    public function testAuthManager(): void
    {
        $authManager = new AuthManager(parent::$enviroment, parent::$config);

        $authManager->auth(true);

        $this->assertNotNull($authManager->getAccessToken());
        $this->assertNotNull($authManager->getId());
        $this->assertNotNull($authManager->getState());
        $this->assertNotNull($authManager->getJwt());

        $this->assertInstanceOf(AccessToken::class, $authManager->getAccessToken());
        $this->assertInstanceOf(Jwt::class, $authManager->getJwt());

        $authManager->logout();

        $this->assertNull($authManager->getAccessToken());
        $this->assertNull($authManager->getId());
        $this->assertNull($authManager->getState());
        $this->assertNull($authManager->getJwt());
    }
}
