<?php

declare(strict_types=1);

namespace Ailos\Sdk\Auth;

use Ailos\Sdk\Auth\Credentials\ClientCredentials;
use Ailos\Sdk\Auth\Credentials\CooperadoCredentials;
use Ailos\Sdk\Auth\Steps\AuthenticateCooperadoStep;
use Ailos\Sdk\Auth\Steps\FetchAccessTokenStep;
use Ailos\Sdk\Auth\Steps\FetchAuthIdStep;
use Ailos\Sdk\Auth\Tokens\AccessToken;
use Ailos\Sdk\Auth\Tokens\AuthId;
use Ailos\Sdk\Storage\Contracts\TokenStoreInterface;
use Ailos\Sdk\Storage\TokenKeys;

class AuthOrchestrator
{
    public function __construct(
        private readonly FetchAccessTokenStep     $fetchAccessTokenStep,
        private readonly FetchAuthIdStep          $fetchAuthIdStep,
        private readonly AuthenticateCooperadoStep $authenticateCooperadoStep,
        private readonly TokenStoreInterface      $tokenStore,
    ) {
    }

    public function run(
        ClientCredentials    $clientCredentials,
        CooperadoCredentials $cooperadoCredentials,
    ): void {
        $accessToken = $this->resolveAccessToken($clientCredentials);
        $authId      = $this->resolveAuthId($accessToken, $cooperadoCredentials);

        $this->authenticateCooperadoStep->execute(
            accessToken:  $accessToken,
            authId:       $authId,
            credentials:  $cooperadoCredentials,
        );
    }

    public function resolveAccessToken(ClientCredentials $credentials): AccessToken
    {
        $cached = $this->tokenStore->get(TokenKeys::ACCESS_TOKEN);

        if ($cached instanceof AccessToken && !$cached->isExpired()) {
            return $cached;
        }

        $accessToken = $this->fetchAccessTokenStep->execute($credentials);

        $this->tokenStore->set(
            key:   TokenKeys::ACCESS_TOKEN,
            value: $accessToken,
            ttl:   3600,
        );

        return $accessToken;
    }

    public function resolveAuthId(
        AccessToken          $accessToken,
        CooperadoCredentials $credentials,
    ): AuthId {
        $cached = $this->tokenStore->get(TokenKeys::AUTH_ID);

        if ($cached instanceof AuthId) {
            return $cached;
        }

        $authId = $this->fetchAuthIdStep->execute($accessToken, $credentials);

        $this->tokenStore->set(
            key:   TokenKeys::AUTH_ID,
            value: $authId,
            ttl:   0,
        );

        return $authId;
    }

    public function storeJwt(string $jwtValue): void
    {
        $jwt = new \Ailos\Sdk\Auth\Tokens\JwtToken($jwtValue);

        $this->tokenStore->set(
            key:   TokenKeys::JWT,
            value: $jwt,
            ttl:   1800,
        );
    }
}
