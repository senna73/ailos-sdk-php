<?php

declare(strict_types=1);

namespace Ailos\Sdk\Collection\Auth\Endpoints;

use Ailos\Sdk\Collection\Auth\Credentials\CooperadoCredentials;
use Ailos\Sdk\Collection\Auth\Tokens\AccessToken;
use Ailos\Sdk\Collection\Auth\Tokens\AuthId;
use Ailos\Sdk\Exceptions\AuthenticationException;
use Ailos\Sdk\Http\Contracts\HttpClientInterface;
use Ailos\Sdk\Http\Environment;

readonly class FetchAuthIdEndpoint
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private Environment         $environment,
    ) {
    }

    public function execute(AccessToken $accessToken, CooperadoCredentials $credentials): AuthId
    {
        try {
            $response = $this->httpClient->post(
                url: $this->environment->authIdUrl(),
                headers: [
                    'Authorization' => $accessToken->bearerHeader(),
                    'Accept' => 'text/plain',
                ],
                body: $credentials->toAuthIdPayload(),
            );
        } catch (\Throwable $e) {
            throw AuthenticationException::failedToFetchAuthId($e->getMessage());
        }

        $this->assertValidResponse($response);

        return new AuthId($this->extractId($response));
    }

    /**
     * @param array<string, mixed> $response
     */
    private function assertValidResponse(array $response): void
    {
        if (empty($this->extractId($response))) {
            throw AuthenticationException::failedToFetchAuthId(
                'Response did not contain a valid auth ID.'
            );
        }
    }

    /**
     * @param array<string, mixed> $response
     */
    private function extractId(array $response): string
    {
        if (isset($response['id']) && is_string($response['id'])) {
            return $response['id'];
        }

        if (isset($response['raw']) && is_string($response['raw'])) {
            return $response['raw'];
        }

        return '';
    }
}
