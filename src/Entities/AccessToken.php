<?php

declare(strict_types=1);

namespace Ailos\Sdk\Entities;

use Ailos\Sdk\Framework\Entity;
use DateTimeImmutable;

class AccessToken extends Entity
{
    private readonly DateTimeImmutable $createdAt;

    public function __construct(
        public readonly string $accessToken,
        public readonly string $tokenType,
        public readonly int $expiresIn,
        public readonly string $scope,
    ) {
        $this->createdAt = new DateTimeImmutable();
    }

    public function isValid(): bool
    {
        $expiresAt = $this->createdAt->modify("+{$this->expiresIn} seconds");

        return new DateTimeImmutable() < $expiresAt;
    }
}
