<?php

declare(strict_types=1);

namespace Ailos\Sdk\Exceptions;

use RuntimeException;

class AilosSdkException extends RuntimeException
{
    public static function withMessage(string $message, int $code = 0): self
    {
        return new self($message, $code);
    }
}
