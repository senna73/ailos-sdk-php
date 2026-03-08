<?php

declare(strict_types=1);

namespace Ailos\Sdk\Auth\Steps;

use Ailos\Sdk\Auth\Credentials\CooperadoCredentials;
use Ailos\Sdk\Auth\Tokens\AccessToken;
use Ailos\Sdk\Auth\Tokens\AuthId;
use Ailos\Sdk\Exceptions\AuthenticationException;
use Ailos\Sdk\Exceptions\InvalidCredentialsException;
use Ailos\Sdk\Http\Contracts\HttpClientInterface;
use Ailos\Sdk\Http\Environment;

class AuthenticateCooperadoStep
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly Environment $environment,
    ) {
    }

    public function execute(
        AccessToken $accessToken,
        AuthId $authId,
        CooperadoCredentials $credentials,
    ): void {
        try {
            $response = $this->httpClient->postForm(
                url: $this->environment->loginUrl($authId->urlEncoded()),
                headers: [
                    "Authorization: {$accessToken->bearerHeader()}",
                ],
                fields: $credentials->toLoginPayload(),
            );
        } catch (\Throwable $e) {
            throw AuthenticationException::failedToAuthenticateCooperado($e->getMessage());
        }

        $this->assertValidResponse($response);
    }

    private function assertValidResponse(array $response): void
    {
        $status = $response['_status_code'] ?? 0;

        if ($status === 401 || $status === 403) {
            throw InvalidCredentialsException::invalidCooperadoCredentials();
        }

        if ($status >= 400) {
            throw AuthenticationException::failedToAuthenticateCooperado(
                "Unexpected response status: {$status}"
            );
        }
    }
}
