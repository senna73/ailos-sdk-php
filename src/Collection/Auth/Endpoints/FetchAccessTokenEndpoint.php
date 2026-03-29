<?php

declare(strict_types=1);

namespace Ailos\Sdk\Collection\Auth\Endpoints;

use Ailos\Sdk\Collection\Auth\Credentials\ClientCredentials;
use Ailos\Sdk\Collection\Auth\Tokens\AccessToken;
use Ailos\Sdk\Exceptions\AuthenticationException;
use Ailos\Sdk\Exceptions\InvalidCredentialsException;
use Ailos\Sdk\Http\Contracts\HttpClientInterface;
use Ailos\Sdk\Http\Environment;

readonly class FetchAccessTokenEndpoint
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private Environment         $environment,
    ) {
    }

    public function execute(ClientCredentials $credentials): AccessToken
    {
        try {
            $response = $this->httpClient->postUrlEncoded(
                url: $this->environment->tokenUrl(),
                headers: [
                    'Authorization' => $credentials->basicAuthHeader(),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                body: 'grant_type=client_credentials',
            );
        } catch (\Throwable $e) {
            throw AuthenticationException::failedToFetchAccessToken($e->getMessage());
        }

        $this->assertValidResponse($response);

        /** @var array<string, mixed> $response */

        $accessToken = $response['access_token'] ?? null;
        $expiresIn   = $response['expires_in'] ?? null;
        $tokenType   = $response['token_type'] ?? null;

        if (!is_string($accessToken) || !is_int($expiresIn) || !is_string($tokenType)) {
            throw AuthenticationException::failedToFetchAccessToken('Invalid response structure.');
        }

        return new AccessToken(
            $accessToken,
            $expiresIn,
            $tokenType
        );
    }

    /**
     * @param array<string, mixed> $response
     */
    private function assertValidResponse(array $response): void
    {
        if (($response['_status_code'] ?? 0) === 401) {
            throw InvalidCredentialsException::invalidClientCredentials();
        }

        if (empty($response['access_token'])) {
            throw AuthenticationException::failedToFetchAccessToken(
                'Response did not contain a valid access_token.'
            );
        }
    }
}
