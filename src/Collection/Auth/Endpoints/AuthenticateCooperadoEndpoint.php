<?php

declare(strict_types=1);

namespace Ailos\Sdk\Collection\Auth\Endpoints;

use Ailos\Sdk\Collection\Auth\Credentials\CooperadoCredentials;
use Ailos\Sdk\Collection\Auth\Tokens\AccessToken;
use Ailos\Sdk\Collection\Auth\Tokens\AuthId;
use Ailos\Sdk\Exceptions\AuthenticationException;
use Ailos\Sdk\Http\Contracts\HttpClientInterface;
use Ailos\Sdk\Http\Environment;

readonly class AuthenticateCooperadoEndpoint
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private Environment         $environment,
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
                    'Authorization' => $accessToken->bearerHeader(),
                ],
                fields: $credentials->toLoginPayload(),
            );
        } catch (\Throwable $e) {
            throw AuthenticationException::failedToAuthenticateCooperado($e->getMessage());
        }

        $this->assertValidResponse($response);
    }

    /**
     * @param array<string, mixed> $response
     */
    private function assertValidResponse(array $response): void
    {
        $status = isset($response['_status_code']) && is_int($response['_status_code'])
            ? $response['_status_code']
            : 0;

        $body = isset($response['raw']) && is_string($response['raw'])
            ? $response['raw']
            : '';

        $error = $this->extractHtmlError($body);

        if ($error !== null) {
            throw AuthenticationException::failedToAuthenticateCooperado($error);
        }

        if ($status >= 400) {
            throw AuthenticationException::failedToAuthenticateCooperado(
                'Unexpected response status: ' . (string) $status
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

        if ($nodes === false || $nodes->length === 0) {
            return null;
        }

        $node = $nodes->item(0);

        if (!$node instanceof \DOMNode) {
            return null;
        }

        return trim($node->textContent);
    }
}
