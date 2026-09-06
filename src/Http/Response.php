<?php

declare(strict_types=1);

namespace Ailos\Sdk\Http;

use JsonException;
use RuntimeException;

final readonly class Response
{
    public function __construct(
        public int $statusCode,
        private string $rawBody,
        /**
         * @var array<string, string>
         */
        public array $headers = [],
    ) {
    }

    public function isEmpty(): bool
    {
        return $this->statusCode === 204 || trim($this->rawBody) === '';
    }

    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * @return array<string, mixed>
     * @throws RuntimeException se o corpo não for JSON válido
     */
    public function json(): array
    {
        if ($this->isEmpty()) {
            return [];
        }

        try {
            $decoded = json_decode($this->rawBody, associative: true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException(
                "Resposta esperada em JSON, mas o corpo não é um JSON válido: {$e->getMessage()}",
                previous: $e,
            );
        }

        if (!is_array($decoded)) {
            throw new RuntimeException(
                'Resposta esperada como objeto/array JSON, mas o corpo decodificado é do tipo ' . get_debug_type($decoded) . '.',
            );
        }

        /** @var array<string, mixed> $decoded */
        return $decoded;
    }

    public function text(): string
    {
        return $this->rawBody;
    }
}
