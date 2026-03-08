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
        $body   = $response['body'] ?? '';

        $error = $this->extractHtmlError($body);

        if ($error !== null) {
            throw InvalidCredentialsException::invalidCooperadoCredentials($error);
        }

        if ($status >= 400) {
            throw AuthenticationException::failedToAuthenticateCooperado(
                "Unexpected response status: {$status}"
            );
        }
    }

    private function extractHtmlError(string $html): ?string
    {
        if (empty($html)) {
            return null;
        }

        libxml_use_internal_errors(true);

        $dom = new \DOMDocument();
        $dom->loadHTML($html);

        $xpath = new \DOMXPath($dom);

        $nodes = $xpath->query("//div[contains(@class,'validation-summary-errors')]//li");

        if ($nodes->length === 0) {
            return null;
        }

        return trim($nodes->item(0)->textContent);
    }
}
