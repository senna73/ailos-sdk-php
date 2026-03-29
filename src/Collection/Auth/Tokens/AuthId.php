<?php

declare(strict_types=1);

namespace Ailos\Sdk\Collection\Auth\Tokens;

class AuthId
{
    public function __construct(
        private readonly string $value,
    ) {
    }

    public function value(): string
    {
        return $this->value;
    }

    public function urlEncoded(): string
    {
        return urlencode($this->value);
    }

    public function isExpired(): bool
    {
        return false;
    }
}
