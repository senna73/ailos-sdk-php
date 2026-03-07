<?php

declare(strict_types=1);

namespace Ailos\Sdk\Support;

class Base64Encoder
{
    public function encode(string $value): string
    {
        return base64_encode($value);
    }

    public function encodeCredentials(string $key, string $secret): string
    {
        return $this->encode("{$key}:{$secret}");
    }
}