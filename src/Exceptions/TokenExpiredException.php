<?php

declare(strict_types=1);

namespace Ailos\Sdk\Exceptions;

class TokenExpiredException extends AuthenticationException
{
    public static function accessTokenExpired(): static
    {
        return new static('Access token has expired. A new authentication cycle is required.');
    }

    public static function jwtExpiredAndCannotRefresh(): static
    {
        return new static('JWT token has expired and cannot be refreshed. Please re-authenticate.');
    }
}
