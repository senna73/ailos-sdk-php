<?php

declare(strict_types=1);

namespace Ailos\Sdk\Exceptions;

final class HttpException extends AilosSdkException
{
    public function __construct(
        string $message,
        private readonly int $statusCode = 0,
        int $code = 0,
    ) {
        parent::__construct($message, $code);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public static function fromResponse(int $statusCode, string $body): self
    {
        return new self(
            message: "HTTP request failed with status {$statusCode}: {$body}",
            statusCode: $statusCode,
        );
    }

    public static function fromConnectionFailure(string $reason): self
    {
        return new self(
            message: "Connection failed: {$reason}",
            statusCode: 0,
        );
    }
}
