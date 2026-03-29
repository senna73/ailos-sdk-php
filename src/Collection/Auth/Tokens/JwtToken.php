<?php

declare(strict_types=1);

namespace Ailos\Sdk\Collection\Auth\Tokens;

use DateTimeImmutable;

class JwtToken
{
    private const EXPIRATION_SECONDS = 1800; // 30 minutos
    private const REFRESH_WINDOW_SECONDS = 300; // janela de 5 min antes de expirar

    private readonly DateTimeImmutable $expiresAt;
    private readonly DateTimeImmutable $refreshableUntil;

    public function __construct(
        private readonly string $value,
        private readonly DateTimeImmutable $createdAt = new DateTimeImmutable(),
    ) {
        $this->expiresAt = $this->createdAt->modify('+' . self::EXPIRATION_SECONDS . ' seconds');
        $this->refreshableUntil = $this->expiresAt;
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

    public function needsRefresh(): bool
    {
        $refreshThreshold = $this->expiresAt->modify('-' . self::REFRESH_WINDOW_SECONDS . ' seconds');

        return new DateTimeImmutable() >= $refreshThreshold;
    }

    public function canRefresh(): bool
    {
        return new DateTimeImmutable() < $this->refreshableUntil;
    }

    public function headerValue(): string
    {
        return $this->value;
    }
}
