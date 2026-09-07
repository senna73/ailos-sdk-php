<?php

declare(strict_types=1);

namespace Ailos\Sdk\Cobranca\Auth;

use CuyZ\Valinor\Mapper\Configurator\MapKeysToCamelCase;
use CuyZ\Valinor\MapperBuilder;
use DateTimeImmutable;

final class AccessToken
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
