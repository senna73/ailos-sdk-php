<?php

declare(strict_types=1);

namespace Ailos\Sdk\Exceptions;

class AuthenticationException extends AilosSdkException
{
    public static function failedToFetchAccessToken(string $reason): static
    {
        return new static("Failed to fetch access token: {$reason}");
    }

    public static function failedToFetchAuthId(string $reason): static
    {
        return new static("Failed to fetch auth ID: {$reason}");
    }

    public static function failedToAuthenticateCooperado(string $reason): static
    {
        return new static("Failed to authenticate cooperado: {$reason}");
    }
}
