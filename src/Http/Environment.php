<?php

declare(strict_types=1);

namespace Ailos\Sdk\Http;

use InvalidArgumentException;

class Environment
{
    private const URLS = [
        'homologacao' => 'https://apiendpointhml.ailos.coop.br',
        'producao'    => 'https://apiendpoint.ailos.coop.br',
    ];

    public function __construct(
        private readonly string $environment = 'homologacao',
    ) {
        if (!array_key_exists($environment, self::URLS)) {
            throw new InvalidArgumentException(
                "Invalid environment '{$environment}'. Allowed: " . implode(', ', array_keys(self::URLS))
            );
        }
    }

    public function baseUrl(): string
    {
        return self::URLS[$this->environment];
    }

    public function tokenUrl(): string
    {
        return $this->baseUrl() . '/token';
    }

    public function authIdUrl(): string
    {
        return $this->baseUrl() . '/ailos/identity/api/v1/autenticacao/login/obter/id';
    }

    public function loginUrl(string $encodedId): string
    {
        return $this->baseUrl() . "/ailos/identity/api/v1/login/index?id={$encodedId}";
    }

    public function refreshUrl(string $encodedJwt): string
    {
        return $this->baseUrl() . "/ailos/identity/api/v1/autenticacao/token/refresh?code={$encodedJwt}";
    }

    public function isProduction(): bool
    {
        return $this->environment === 'producao';
    }
}