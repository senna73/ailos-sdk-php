<?php

declare(strict_types=1);

namespace Ailos\Sdk\Auth\Tokens;

use DateTimeImmutable;

class AccessToken
{
    private readonly DateTimeImmutable $expiresAt;

    public function __construct(
        private readonly string $value,
        private readonly int $expiresIn,
        private readonly DateTimeImmutable $createdAt = new DateTimeImmutable(),
    ) {
        $this->expiresAt = $this->createdAt->modify("+{$expiresIn} seconds");
    }

    public function value(): string
    {
        return $this->value;
    }

    public function expiresAt(): DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function isExpired(): bool
    {
        return new DateTimeImmutable() >= $this->expiresAt;
    }

    public function bearerHeader(): string
    {
        return "Bearer {$this->value}";
    }
}