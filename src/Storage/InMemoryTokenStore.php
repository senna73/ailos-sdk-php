<?php

declare(strict_types=1);

namespace Ailos\Sdk\Storage;

use Ailos\Sdk\Storage\Contracts\TokenStoreInterface;

class InMemoryTokenStore implements TokenStoreInterface
{
    private array $store = [];
    private array $expirations = [];

    public function get(string $key): mixed
    {
        if (!$this->has($key)) {
            return null;
        }

        return $this->store[$key];
    }

    public function set(string $key, mixed $value, int $ttl = 0): void
    {
        $this->store[$key] = $value;

        if ($ttl > 0) {
            $this->expirations[$key] = time() + $ttl;
        }
    }

    public function forget(string $key): void
    {
        unset($this->store[$key], $this->expirations[$key]);
    }

    public function has(string $key): bool
    {
        if (!array_key_exists($key, $this->store)) {
            return false;
        }

        if ($this->isExpired($key)) {
            $this->forget($key);
            return false;
        }

        return true;
    }

    public function flush(): void
    {
        $this->store = [];
        $this->expirations = [];
    }

    private function isExpired(string $key): bool
    {
        if (!array_key_exists($key, $this->expirations)) {
            return false;
        }

        return time() >= $this->expirations[$key];
    }
}