<?php

declare(strict_types=1);

namespace Ailos\Sdk\Storage\Contracts;

interface TokenStoreInterface
{
    public function get(string $key): mixed;

    public function set(string $key, mixed $value, int $ttl = 0): void;

    public function forget(string $key): void;

    public function has(string $key): bool;
}