<?php

declare(strict_types=1);

namespace Ailos\Sdk;

use Ailos\Sdk\Auth\AuthOrchestrator;
use Ailos\Sdk\Auth\Credentials\ClientCredentials;
use Ailos\Sdk\Auth\Credentials\CooperadoCredentials;
use Ailos\Sdk\Auth\Steps\AuthenticateCooperadoStep;
use Ailos\Sdk\Auth\Steps\FetchAccessTokenStep;
use Ailos\Sdk\Auth\Steps\FetchAuthIdStep;
use Ailos\Sdk\Auth\TokenRefresher;
use Ailos\Sdk\Auth\Tokens\JwtToken;
use Ailos\Sdk\Exceptions\AuthenticationException;
use Ailos\Sdk\Http\AilosHttpClient;
use Ailos\Sdk\Http\Contracts\HttpClientInterface;
use Ailos\Sdk\Http\Environment;
use Ailos\Sdk\Storage\Contracts\TokenStoreInterface;
use Ailos\Sdk\Storage\InMemoryTokenStore;

class AilosSdk
{
    private readonly AuthOrchestrator  $orchestrator;
    private readonly TokenRefresher    $tokenRefresher;
    private readonly TokenStoreInterface $tokenStore;

    public function __construct(
        private readonly ClientCredentials    $clientCredentials,
        private readonly CooperadoCredentials $cooperadoCredentials,
        string                                $environment = 'homologacao',
        ?HttpClientInterface                  $httpClient = null,
        ?TokenStoreInterface                  $tokenStore = null,
    ) {
        $env        = new Environment($environment);
        $http       = $httpClient ?? new AilosHttpClient();
        $this->tokenStore = $tokenStore ?? new InMemoryTokenStore();

        $fetchAccessTokenStep      = new FetchAccessTokenStep($http, $env);
        $fetchAuthIdStep           = new FetchAuthIdStep($http, $env);
        $authenticateCooperadoStep = new AuthenticateCooperadoStep($http, $env);

        $this->orchestrator = new AuthOrchestrator(
            fetchAccessTokenStep:      $fetchAccessTokenStep,
            fetchAuthIdStep:           $fetchAuthIdStep,
            authenticateCooperadoStep: $authenticateCooperadoStep,
            tokenStore:                $this->tokenStore,
        );

        $this->tokenRefresher = new TokenRefresher(
            httpClient:   $http,
            environment:  $env,
            tokenStore:   $this->tokenStore,
            orchestrator: $this->orchestrator,
        );
    }

    /**
     * Inicia o fluxo completo de autenticação (Etapas 1, 2 e 3).
     * O JWT será enviado pela Ailos para a urlCallback configurada.
     * Após receber o JWT no callback, chame handleCallback() para armazená-lo.
     */
    public function authenticate(): void
    {
        $this->orchestrator->run(
            clientCredentials:    $this->clientCredentials,
            cooperadoCredentials: $this->cooperadoCredentials,
        );
    }

    /**
     * Deve ser chamado pelo endpoint de callback da aplicação
     * assim que o JWT (x-ailos-authentication) for recebido da Ailos.
     */
    public function handleCallback(string $jwt): void
    {
        $this->orchestrator->storeJwt($jwt);
    }

    /**
     * Retorna um JWT válido — renova automaticamente se necessário.
     * Lança AuthenticationException se authenticate() nunca foi chamado.
     */
    public function getJwt(): JwtToken
    {
        return $this->tokenRefresher->getValidJwt(
            clientCredentials:    $this->clientCredentials,
            cooperadoCredentials: $this->cooperadoCredentials,
        );
    }

    /**
     * Retorna o valor string do JWT pronto para uso em headers.
     * Atalho conveniente para $sdk->getJwt()->value()
     */
    public function getJwtValue(): string
    {
        return $this->getJwt()->value();
    }

    /**
     * Verifica se há um JWT armazenado e válido disponível.
     */
    public function isAuthenticated(): bool
    {
        try {
            $jwt = $this->getJwt();
            return !$jwt->isExpired();
        } catch (AuthenticationException) {
            return false;
        }
    }

    /**
     * Limpa todos os tokens armazenados.
     * Force um novo ciclo completo de autenticação.
     */
    public function logout(): void
    {
        if ($this->tokenStore instanceof InMemoryTokenStore) {
            $this->tokenStore->flush();
        }
    }
}
