<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests\Integration\Cobranca;

use Ailos\Sdk\Cobranca\Auth\AccessToken;
use Ailos\Sdk\Cobranca\Auth\Auth;
use Ailos\Sdk\Cobranca\Auth\Jwt;
use Ailos\Sdk\Tests\CobrancaTestCase;

class AuthManagerTest extends CobrancaTestCase
{
    protected function setUp(): void
    {
        // roda ANTES de cada método de teste
        parent::setUp();
        new Auth(parent::$context)->logout();
    }

    protected function tearDown(): void
    {
        // roda DEPOIS de cada método de teste
        parent::tearDown();

        new Auth(parent::$context)->logout();
    }

    public function testAuth(): void
    {
        $authManager = new Auth(parent::$context);

        $authManager->auth();

        $this->assertNotNull($authManager->getAccessToken());
        $this->assertNotNull($authManager->getId());
        $this->assertNotNull($authManager->getState());
        $this->assertNotNull($authManager->getJwt());

        $this->assertInstanceOf(AccessToken::class, $authManager->getAccessToken());
        $this->assertInstanceOf(Jwt::class, $authManager->getJwt());
    }

    public function testLogout(): void
    {
        $authManager = new Auth(parent::$context);

        $authManager->auth();

        $authManager->logout();

        $this->assertNull($authManager->getAccessToken());
        $this->assertNull($authManager->getId());
        $this->assertNull($authManager->getState());
        $this->assertNull($authManager->getJwt());
    }

    public function testAuthUsesCachedTokens(): void
    {
        $authManager = new Auth(parent::$context);

        $authManager->auth();

        $accessToken = $authManager->getAccessToken();
        $id = $authManager->getId();
        $state = $authManager->getState();
        $jwt = $authManager->getJwt();

        $this->assertNotNull($accessToken);
        $this->assertNotNull($id);
        $this->assertNotNull($state);
        $this->assertNotNull($jwt);

        $cachedAuthManager = new Auth(parent::$context);
        $cachedAuthManager->auth();

        $cachedAccessToken = $cachedAuthManager->getAccessToken();
        $cachedId = $cachedAuthManager->getId();
        $cachedState = $cachedAuthManager->getState();
        $cachedJwt = $cachedAuthManager->getJwt();

        $this->assertSame($accessToken->accessToken, $cachedAccessToken?->accessToken);
        $this->assertSame($id, $cachedId);
        $this->assertSame($state, $cachedState);
        $this->assertSame($jwt->code, $cachedJwt?->code);
    }

    public function testAuthRefreshesExpiredJwt(): void
    {
        $authManager = new Auth(parent::$context);

        $authManager->auth();

        $originalJwt = $authManager->getJwt();
        $this->assertNotNull($originalJwt);

        $expiredJwt = new Jwt($originalJwt->state, $originalJwt->code, 0);
        parent::$context->config->storage->set('jwt', $expiredJwt, 3600);

        $authManager->auth();

        $refreshedJwt = $authManager->getJwt();

        $this->assertNotNull($refreshedJwt);
        $this->assertNotSame($expiredJwt->code, $refreshedJwt->code);
        $this->assertTrue($refreshedJwt->isValid());
    }
}
