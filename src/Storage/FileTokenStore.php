<?php

declare(strict_types=1);

namespace Ailos\Sdk\Storage;

use Ailos\Sdk\Exceptions\AilosSdkException;
use Ailos\Sdk\Storage\Contracts\TokenStoreInterface;

class FileTokenStore implements TokenStoreInterface
{
    private const string STORE_PATH = __DIR__ . '/../../public/temp/tokens/';

    public function __construct()
    {
        if (!is_dir(self::STORE_PATH)) {
            mkdir(self::STORE_PATH, 0755, recursive: true);
        }
    }

    public function get(string $key): mixed
    {
        $path = $this->resolvePath($key);

        if (!file_exists($path)) {
            return null;
        }

        $contents = file_get_contents($path);

        return $contents !== false
            ? unserialize($contents, ['allowed_classes' => true])
            : null;
    }

    public function set(string $key, mixed $value): void
    {
        $this->forget($key);

        $path    = $this->resolvePath($key);
        $tmpPath = $path . '.tmp.' . uniqid('', more_entropy: true);

        $written = file_put_contents($tmpPath, serialize($value), LOCK_EX);

        if ($written === false) {
            throw new AilosSdkException("Falha ao escrever: $tmpPath");
        }

        if (!rename($tmpPath, $path)) {
            unlink($tmpPath);
            throw new AilosSdkException("Falha ao salvar: $path");
        }
    }

    public function forget(string $key): void
    {
        $path = $this->resolvePath($key);

        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function clear(): void
    {
        $files = glob(self::STORE_PATH . '*');

        if ($files === false) {
            return;
        }

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function has(string $key): bool
    {
        return file_exists($this->resolvePath($key));
    }

    private function resolvePath(string $key): string
    {
        return self::STORE_PATH . basename($key);
    }
}
