<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests\Auth\Tokens;

use Ailos\Sdk\Auth\Tokens\AccessToken;
use Ailos\Sdk\Auth\Tokens\AuthId;
use Ailos\Sdk\Auth\Tokens\JwtToken;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class TokensTest extends TestCase
{
    // AccessToken
    public function test_access_token_is_not_expired_when_freshly_created(): void
    {
        $token = new AccessToken('token-value', 3600);

        $this->assertFalse($token->isExpired());
    }

    public function test_access_token_is_expired_when_created_in_the_past(): void
    {
        $pastDate = new DateTimeImmutable('-2 hours');
        $token = new AccessToken('token-value', 3600, $pastDate);

        $this->assertTrue($token->isExpired());
    }

    public function test_access_token_returns_correct_bearer_header(): void
    {
        $token = new AccessToken('abc123', 3600);

        $this->assertSame('Bearer abc123', $token->bearerHeader());
    }

    public function test_access_token_returns_its_value(): void
    {
        $token = new AccessToken('my-access-token', 3600);

        $this->assertSame('my-access-token', $token->value());
    }

    public function test_access_token_expires_at_is_correct(): void
    {
        $now = new DateTimeImmutable();
        $token = new AccessToken('token', 3600, $now);

        $expected = $now->modify('+3600 seconds');

        $this->assertSame(
            $expected->getTimestamp(),
            $token->expiresAt()->getTimestamp(),
        );
    }

    // AuthId
    public function test_auth_id_never_expires(): void
    {
        $authId = new AuthId('some-id-value');

        $this->assertFalse($authId->isExpired());
    }

    public function test_auth_id_returns_url_encoded_value(): void
    {
        $authId = new AuthId('id with spaces & special=chars');

        $this->assertSame(urlencode('id with spaces & special=chars'), $authId->urlEncoded());
    }

    public function test_auth_id_returns_raw_value(): void
    {
        $authId = new AuthId('raw-id-123');

        $this->assertSame('raw-id-123', $authId->value());
    }

    // JwtToken
    public function test_jwt_is_not_expired_when_freshly_created(): void
    {
        $token = new JwtToken('jwt-value');

        $this->assertFalse($token->isExpired());
    }

    public function test_jwt_is_expired_when_created_40_minutes_ago(): void
    {
        $past = new DateTimeImmutable('-40 minutes');
        $token = new JwtToken('jwt-value', $past);

        $this->assertTrue($token->isExpired());
    }

    public function test_jwt_needs_refresh_when_within_5_minutes_of_expiry(): void
    {
        $past = new DateTimeImmutable('-26 minutes');
        $token = new JwtToken('jwt-value', $past);

        $this->assertTrue($token->needsRefresh());
    }

    public function test_jwt_does_not_need_refresh_when_far_from_expiry(): void
    {
        $token = new JwtToken('jwt-value', new DateTimeImmutable());

        $this->assertFalse($token->needsRefresh());
    }

    public function test_jwt_can_refresh_before_expiry(): void
    {
        $token = new JwtToken('jwt-value', new DateTimeImmutable());

        $this->assertTrue($token->canRefresh());
    }

    public function test_jwt_cannot_refresh_after_expiry(): void
    {
        $past = new DateTimeImmutable('-40 minutes');
        $token = new JwtToken('jwt-value', $past);

        $this->assertFalse($token->canRefresh());
    }

    public function test_jwt_returns_header_value(): void
    {
        $token = new JwtToken('my.jwt.token');

        $this->assertSame('my.jwt.token', $token->headerValue());
    }
}
