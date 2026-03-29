<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests\Integration\Auth;

use Ailos\Sdk\Tests\Integration\IntegrationTestCase;

class AuthenticationTest extends IntegrationTestCase
{
    public function test_auth_from_real_api(): void
    {
        $this->sdk->auth->authenticate();
        self::assertTrue(true);
    }
}
