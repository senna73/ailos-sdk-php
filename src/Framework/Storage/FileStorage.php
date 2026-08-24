<?php

declare(strict_types=1);

namespace Ailos\Sdk\Framework\Storage;

use Override;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

final class FileStorage implements IStorage
{
    public function __construct(
        private FilesystemAdapter $storage = new FilesystemAdapter('ailos', 0, __DIR__ . '/../../storage')
    ) {}

    #[Override]
    public function get(string $key): mixed
    {
        $item = $this->storage->getItem($key);

        if (!$item->isHit()) {
            return null;
        }

        return $item->get();
    }

    #[Override]
    public function set(string $key, mixed $value, int $ttl = 0): void
    {
        $item = $this->storage->getItem($key);
        $item->set($value);

        if ($ttl > 0) {
            $item->expiresAfter($ttl);
        }

        $this->storage->save($item);
    }

    #[Override]
    public function delete(string $key): void
    {
        $this->storage->deleteItem($key);
    }
}