<?php

declare(strict_types=1);

namespace Ailos\Sdk\Auth\Credentials;

use Ailos\Sdk\Exceptions\InvalidCredentialsException;
use Ailos\Sdk\Support\Base64Encoder;

class ClientCredentials
{
    public function __construct(
        private readonly string $consumerKey,
        private readonly string $consumerSecret,
        private readonly Base64Encoder $encoder = new Base64Encoder(),
    ) {
        $this->validate();
    }

    public function consumerKey(): string
    {
        return $this->consumerKey;
    }

    public function consumerSecret(): string
    {
        return $this->consumerSecret;
    }

    public function basicAuthHeader(): string
    {
        $encoded = $this->encoder->encodeCredentials(
            $this->consumerKey,
            $this->consumerSecret,
        );

        return "Basic {$encoded}";
    }

    private function validate(): void
    {
        if (empty(trim($this->consumerKey)) || empty(trim($this->consumerSecret))) {
            throw InvalidCredentialsException::invalidClientCredentials();
        }
    }
}
