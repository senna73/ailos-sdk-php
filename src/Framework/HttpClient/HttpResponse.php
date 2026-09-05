<?php

declare(strict_types=1);

namespace Ailos\Sdk\Framework\HttpClient;

final class HttpResponse
{
    public function __construct(
        private readonly int $statusCode,
        private readonly string $body,
        /** @var array<string, mixed> */
        private readonly array $headers = [],
    ) {
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return array<string, mixed>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return array<string, mixed>
     * @throws \JsonException
     */
    public function json(): array
    {
        $data = json_decode($this->body, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($data) || array_is_list($data)) {
            throw new \UnexpectedValueException('A resposta JSON deve ser um objeto.');
        }

        /** @var array<string, mixed> $data */
        return $data;
    }

    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }
}
