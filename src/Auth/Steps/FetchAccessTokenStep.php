<?php

declare(strict_types=1);

namespace Ailos\Sdk\Auth\Steps;

use Ailos\Sdk\Auth\Credentials\ClientCredentials;
use Ailos\Sdk\Auth\Tokens\AccessToken;
use Ailos\Sdk\Exceptions\AuthenticationException;
use Ailos\Sdk\Exceptions\InvalidCredentialsException;
use Ailos\Sdk\Http\Contracts\HttpClientInterface;
use Ailos\Sdk\Http\Environment;

class FetchAccessTokenStep
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly Environment $environment,
    ) {
    }

    public function execute(ClientCredentials $credentials): AccessToken
    {
        try {
            $response = $this->httpClient->postUrlEncoded(
                url: $this->environment->tokenUrl(),
                headers: [
                    "Authorization: {$credentials->basicAuthHeader()}",
                    'Accept: application/json',
                ],
                body: 'grant_type=client_credentials',
            );
        } catch (\Throwable $e) {
            throw AuthenticationException::failedToFetchAccessToken($e->getMessage());
        }

        $this->assertValidResponse($response);

        return new AccessToken(
            value: $response['access_token'],
            expiresIn: (int) ($response['expires_in'] ?? 3600),
        );
    }

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
