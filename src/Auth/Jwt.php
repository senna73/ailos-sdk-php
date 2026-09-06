<?php

declare(strict_types=1);

namespace Ailos\Sdk\Auth;

use CuyZ\Valinor\Mapper\Configurator\MapKeysToCamelCase;
use CuyZ\Valinor\MapperBuilder;
use DateTimeImmutable;

final class Jwt
{
    private readonly DateTimeImmutable $createdAt;

    public function __construct(
        public readonly string $state,
        public readonly string $code,
        public readonly int $expiresIn = 1800, // 30 minutos
    ) {
        $this->createdAt = new DateTimeImmutable();
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $mapper = (new MapperBuilder())->configureWith(new MapKeysToCamelCase())->mapper();
        return $mapper->map(self::class, $data);
    }

    public function isValid(): bool
    {
        $expiresAt = $this->createdAt->modify("+{$this->expiresIn} seconds");

        return new DateTimeImmutable() < $expiresAt;
    }
}
