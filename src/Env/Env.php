<?php

declare(strict_types=1);

namespace Ailos\Sdk\Env;

use Dotenv\Dotenv;
use Dotenv\Repository\RepositoryBuilder;
use Dotenv\Repository\RepositoryInterface;

final class Env
{
    private static ?RepositoryInterface $repository = null;

    private function __construct()
    {
    }

    private static function repository(): RepositoryInterface
    {
        if (self::$repository === null) {
            self::$repository = RepositoryBuilder::createWithDefaultAdapters()->make();

            Dotenv::create(self::$repository, PROJECT_ROOT, '.env')->safeLoad();
        }

        return self::$repository;
    }

    public static function env(string $key, ?string $default = null): ?string
    {
        $value = self::repository()->get($key);

        return $value ?? $default;
    }

    public static function requiredEnv(string $key): string
    {
        $value = self::env($key);

        if ($value === null || $value === '') {
            throw new \RuntimeException("Variável de ambiente obrigatória não definida: {$key}");
        }

        return $value;
    }
}
