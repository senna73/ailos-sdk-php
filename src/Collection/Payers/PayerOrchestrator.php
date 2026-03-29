<?php

declare(strict_types=1);

namespace Ailos\Sdk\Collection\Payers;

use Ailos\Sdk\Collection\Auth\AuthOrchestrator;
use Ailos\Sdk\Collection\Payers\Endpoints\RegisterPayerEndpoint;
use Ailos\Sdk\Collection\Payers\Pagador\Pagador;
use Ailos\Sdk\Http\Contracts\HttpClientInterface;
use Ailos\Sdk\Http\Environment;

readonly class PayerOrchestrator
{
    public function __construct(
        private Environment          $environment,
        private HttpClientInterface  $httpClient,
        private AuthOrchestrator $authOrchestrator,
    ) {
    }

    public function registerPayer(Pagador $pagador): void
    {
        new RegisterPayerEndpoint(
            httpClient: $this->httpClient,
            environment: $this->environment,
            authOrchestrator: $this->authOrchestrator,
        )->execute($pagador);
    }
}
