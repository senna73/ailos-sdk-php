<?php

declare(strict_types=1);

namespace Ailos\Sdk;

use Ailos\Sdk\Collection\Auth\AuthOrchestrator;
use Ailos\Sdk\Collection\Auth\Credentials\ClientCredentials;
use Ailos\Sdk\Collection\Auth\Credentials\CooperadoCredentials;
use Ailos\Sdk\Collection\Payers\PayerOrchestrator;
use Ailos\Sdk\Http\AilosHttpClient;
use Ailos\Sdk\Http\Contracts\HttpClientInterface;
use Ailos\Sdk\Http\Environment;
use Ailos\Sdk\Storage\Contracts\TokenStoreInterface;
use Ailos\Sdk\Storage\FileTokenStore;

readonly class AilosSdk
{
    private AuthOrchestrator $authOrchestrator;

    public function __construct(
        private ClientCredentials    $clientCredentials,
        private CooperadoCredentials $cooperadoCredentials,
        private Environment          $environment = new Environment('homologacao'),
        private HttpClientInterface $httpClient = new AilosHttpClient(),
        private TokenStoreInterface $tokenStore = new FileTokenStore(),
    ) {
        $this->authOrchestrator = new AuthOrchestrator(
            clientCredentials: $this->clientCredentials,
            cooperadoCredentials: $this->cooperadoCredentials,
            environment: $this->environment,
            httpClient: $this->httpClient,
            tokenStore: $this->tokenStore
        );
    }

    public function auth(): AuthOrchestrator
    {
        return $this->authOrchestrator;
    }

    public function pagador(): PayerOrchestrator
    {
        return new PayerOrchestrator(
            environment: $this->environment,
            httpClient: $this->httpClient,
            authOrchestrator: $this->authOrchestrator
        );
    }
}
