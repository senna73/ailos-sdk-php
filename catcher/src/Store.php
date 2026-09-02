<?php

declare(strict_types=1);

namespace Ailos\Sdk\Catcher;

class Store
{
    private const TTL_SECONDS = 300; // 5 minutos é suficiente pro fluxo de callback
    private const PREFIX = 'catcher:';

    public static function put(string $correlationId, string $payload): void
    {
        apcu_store(self::PREFIX . $correlationId, $payload, self::TTL_SECONDS);
    }

    public static function get(string $correlationId): ?string
    {
        $value = apcu_fetch(self::PREFIX . $correlationId, $success);
        return $success ? $value : null;
    }

    public static function delete(string $correlationId): void
    {
        apcu_delete(self::PREFIX . $correlationId);
    }
}