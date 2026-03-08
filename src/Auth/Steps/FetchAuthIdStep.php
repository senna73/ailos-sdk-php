<?php

declare(strict_types=1);

namespace Ailos\Sdk\Auth\Steps;

use Ailos\Sdk\Auth\Credentials\CooperadoCredentials;
use Ailos\Sdk\Auth\Tokens\AccessToken;
use Ailos\Sdk\Auth\Tokens\AuthId;
use Ailos\Sdk\Exceptions\AuthenticationException;
use Ailos\Sdk\Http\Contracts\HttpClientInterface;
use Ailos\Sdk\Http\Environment;

class FetchAuthIdStep
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly Environment $environment,
    ) {
    }

    public function execute(AccessToken $accessToken, CooperadoCredentials $credentials): AuthId
    {
        try {
            $response = $this->httpClient->post(
                url: $this->environment->authIdUrl(),
                headers: [
                    "Authorization: {$accessToken->bearerHeader()}",
                    'Accept: text/plain',
                ],
                body: $credentials->toAuthIdPayload(),
            );
        } catch (\Throwable $e) {
            throw AuthenticationException::failedToFetchAuthId($e->getMessage());
        }

        $this->assertValidResponse($response);

        return new AuthId($this->extractId($response));
    }

    private function assertValidResponse(array $response): void
    {
        if (empty($this->extractId($response))) {
            throw AuthenticationException::failedToFetchAuthId(
                'Response did not contain a valid auth ID.'
            );
        }
    }

    private function extractId(array $response): string
    {
        // A API pode retornar o ID diretamente como string no campo 'raw'
        // ou dentro de um campo 'id' dependendo do Content-Type da resposta
        return (string) ($response['id'] ?? $response['raw'] ?? '');
    }
}
