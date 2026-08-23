<?php

declare(strict_types=1);

namespace Ailos\Sdk\Entities;

use Ailos\Sdk\Framework\Entity;
use DateTimeImmutable;

class Jwt extends Entity
{
    private readonly DateTimeImmutable $createdAt;

    public function __construct(
        public readonly string $state,
        public readonly string $code,
        public readonly int $expiresIn = 1800, // 30 minutos
    ) {
        $this->createdAt = new DateTimeImmutable();
    }

    public function isValid(): bool
    {
        $expiresAt = $this->createdAt->modify("+{$this->expiresIn} seconds");

        return new DateTimeImmutable() < $expiresAt;
    }
}