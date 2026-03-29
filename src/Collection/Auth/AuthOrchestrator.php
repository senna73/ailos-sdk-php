<?php

declare(strict_types=1);

namespace Ailos\Sdk\Collection\Auth;

use Ailos\Sdk\Collection\Auth\Callback\CallbackHandler;
use Ailos\Sdk\Collection\Auth\Credentials\ClientCredentials;
use Ailos\Sdk\Collection\Auth\Credentials\CooperadoCredentials;
use Ailos\Sdk\Collection\Auth\Endpoints\AuthenticateCooperadoEndpoint;
use Ailos\Sdk\Collection\Auth\Endpoints\FetchAccessTokenEndpoint;
use Ailos\Sdk\Collection\Auth\Endpoints\FetchAuthIdEndpoint;
use Ailos\Sdk\Collection\Auth\Tokens\AccessToken;
use Ailos\Sdk\Collection\Auth\Tokens\AuthId;
use Ailos\Sdk\Collection\Auth\Tokens\JwtToken;
use Ailos\Sdk\Exceptions\AuthenticationException;
use Ailos\Sdk\Http\Contracts\HttpClientInterface;
use Ailos\Sdk\Http\Environment;
use Ailos\Sdk\Storage\Contracts\TokenStoreInterface;
use Ailos\Sdk\Storage\TokenKeys;

readonly class AuthOrchestrator
{
    public function __construct(
        private ClientCredentials    $clientCredentials,
        private CooperadoCredentials $cooperadoCredentials,
        private Environment          $environment,
        private HttpClientInterface  $httpClient,
        private TokenStoreInterface  $tokenStore,
    ) {
    }

    /**
     * Inicia o fluxo completo de autenticação.
     * O JWT será enviado pela Ailos para a urlCallback configurada.
     * Após receber o JWT no callback, chame handleCallback() para armazená-lo.
     */
    public function authenticate(): void
    {
        $accessToken = $this->accessToken();
        $authId = $this->authId($accessToken);
        $this->jwt($accessToken, $authId);
    }

    public function accessToken(): AccessToken
    {
        $accessToken = new FetchAccessTokenEndpoint(
            $this->httpClient,
            $this->environment
        )->execute($this->clientCredentials);

        $this->tokenStore->set(
            key:   TokenKeys::ACCESS_TOKEN,
            value: $accessToken
        );

        return $accessToken;
    }

    public function authId(AccessToken $accessToken): AuthId
    {
        $authId = new FetchAuthIdEndpoint(
            $this->httpClient,
            $this->environment
        )->execute($accessToken, $this->cooperadoCredentials);

        $this->tokenStore->set(
            key:   TokenKeys::AUTH_ID,
            value: $authId,
        );

        return $authId;
    }

    public function jwt(AccessToken $accessToken, AuthId $authId): JwtToken
    {
        new AuthenticateCooperadoEndpoint(
            $this->httpClient,
            $this->environment
        )->execute(
            accessToken:  $accessToken,
            authId:       $authId,
            credentials:  $this->cooperadoCredentials
        );

        $timeoutMs = 10000;
        $deadline = microtime(true) + ($timeoutMs / 1000);

        while (microtime(true) < $deadline) {

            $jwt = $this->tokenStore->get(TokenKeys::JWT);

            if (!$jwt instanceof JwtToken || $jwt->isExpired()) {
                usleep(200_000);
                continue;
            }

            return $jwt;
        }

        throw new AuthenticationException("JWT não foi recebido no callback dentro de {$timeoutMs}ms.");
    }

    /**
     * Retorna um handler pronto para processar o endpoint de callback.
     */
    public function callbackHandler(): CallbackHandler
    {
        return new CallbackHandler($this->tokenStore);
    }

    public function getJwtToken(): string
    {
        $jwt = $this->tokenStore->get(TokenKeys::JWT);

        if (!$jwt instanceof JwtToken || $jwt->isExpired()) {
            $this->authenticate();
            $jwt = $this->tokenStore->get(TokenKeys::JWT);
        }

        if (!$jwt instanceof JwtToken) {
            throw new \RuntimeException('JWT token not available after authentication.');
        }

        return $jwt->value();
    }

    public function getAccessToken(): string
    {
        $accessToken = $this->tokenStore->get(TokenKeys::ACCESS_TOKEN);

        if (!$accessToken instanceof AccessToken || $accessToken->isExpired()) {
            $this->authenticate();
            $accessToken = $this->tokenStore->get(TokenKeys::ACCESS_TOKEN);
        }

        if (!$accessToken instanceof AccessToken) {
            throw new \RuntimeException('Access token not available after authentication.');
        }

        return $accessToken->value();
    }

}
