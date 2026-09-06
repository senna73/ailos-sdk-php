<?php

declare(strict_types=1);

namespace Ailos\Sdk\Auth\Storage;

use RuntimeException;

final class TokenStorage implements ITokenStorage
{
    public function __construct()
    {
        if (!\extension_loaded('apcu') || !\ini_get('apc.enabled')) {
            throw new RuntimeException('A extensão APCu não está instalada ou habilitada.');
        }
    }

    public function get(string $key): mixed
    {
        $value = apcu_fetch($key, $success);

        if (!$success) {
            return null;
        }

        return $value;
    }

    public function set(string $key, mixed $value, int $ttl = 0): void
    {
        $success = apcu_store($key, $value, $ttl);

        if ($success === false) {
            throw new RuntimeException(sprintf('Falha ao armazenar a chave "%s" no APCu.', $key));
        }
    }

    public function delete(string $key): void
    {
        apcu_delete($key);
    }
}
