<?php

declare(strict_types=1);

namespace Ailos\Sdk\Auth;

use Ailos\Sdk\Auth\Credentials\ClientCredentials;
use Ailos\Sdk\Auth\Credentials\CooperadoCredentials;
use Ailos\Sdk\Auth\Tokens\JwtToken;
use Ailos\Sdk\Exceptions\AuthenticationException;
use Ailos\Sdk\Exceptions\TokenExpiredException;
use Ailos\Sdk\Http\Contracts\HttpClientInterface;
use Ailos\Sdk\Http\Environment;
use Ailos\Sdk\Storage\Contracts\TokenStoreInterface;
use Ailos\Sdk\Storage\TokenKeys;

class TokenRefresher
{
    public function __construct(
        private readonly HttpClientInterface  $httpClient,
        private readonly Environment          $environment,
        private readonly TokenStoreInterface  $tokenStore,
        private readonly AuthOrchestrator     $orchestrator,
    ) {
    }

    public function getValidJwt(
        ClientCredentials    $clientCredentials,
        CooperadoCredentials $cooperadoCredentials,
    ): JwtToken {
        $jwt = $this->tokenStore->get(TokenKeys::JWT);

        if (!$jwt instanceof JwtToken) {
            throw AuthenticationException::withMessage(
                'No JWT found in store. Call authenticate() first.'
            );
        }

        if (!$jwt->needsRefresh()) {
            return $jwt;
        }

        if ($jwt->canRefresh()) {
            return $this->refresh($clientCredentials, $jwt);
        }

        return $this->forceReAuthentication($clientCredentials, $cooperadoCredentials);
    }

    public function refresh(
        ClientCredentials $clientCredentials,
        JwtToken          $jwt,
    ): JwtToken {
        $accessToken = $this->orchestrator->resolveAccessToken($clientCredentials);

        try {
            $response = $this->httpClient->get(
                url: $this->environment->refreshUrl(urlencode($jwt->value())),
                headers: [
                    "Authorization: {$accessToken->bearerHeader()}",
                ],
            );
        } catch (\Throwable $e) {
            throw TokenExpiredException::jwtExpiredAndCannotRefresh();
        }

        $newJwtValue = $response['raw'] ?? $response['token'] ?? '';

        if (empty($newJwtValue)) {
            throw TokenExpiredException::jwtExpiredAndCannotRefresh();
        }

        $newJwt = new JwtToken($newJwtValue);

        $this->tokenStore->set(
            key:   TokenKeys::JWT,
            value: $newJwt,
            ttl:   1800,
        );

        return $newJwt;
    }

    private function forceReAuthentication(
        ClientCredentials    $clientCredentials,
        CooperadoCredentials $cooperadoCredentials,
    ): JwtToken {
        $this->tokenStore->forget(TokenKeys::JWT);
        $this->tokenStore->forget(TokenKeys::ACCESS_TOKEN);

        $this->orchestrator->run($clientCredentials, $cooperadoCredentials);

        $jwt = $this->tokenStore->get(TokenKeys::JWT);

        if (!$jwt instanceof JwtToken) {
            throw AuthenticationException::withMessage(
                'Re-authentication completed but JWT was not stored. Ensure the callback has been handled.'
            );
        }

        return $jwt;
    }
}
