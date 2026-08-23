<?php

declare(strict_types=1);

namespace Ailos\Sdk\Framework\Storage;

interface IStorage
{
    public function get(string $key): ?mixed;
    
    public function set(string $key, mixed $value, int $ttl = 0): void;

    public function delete(string $key): void;
}