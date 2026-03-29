<?php

declare(strict_types=1);

namespace Ailos\Sdk\Collection\Auth\Credentials;

use Ailos\Sdk\Exceptions\InvalidCredentialsException;
use Ailos\Sdk\Support\Base64Encoder;

readonly class ClientCredentials
{
    public function __construct(
        private string        $consumerKey,
        private string        $consumerSecret,
        private Base64Encoder $encoder = new Base64Encoder(),
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
