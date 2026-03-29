<?php

declare(strict_types=1);

namespace Ailos\Sdk\Collection\Payers\Endpoints;

use Ailos\Sdk\Collection\Auth\AuthOrchestrator;
use Ailos\Sdk\Collection\Payers\Pagador\Pagador;
use Ailos\Sdk\Exceptions\AilosSdkException;
use Ailos\Sdk\Http\Contracts\HttpClientInterface;
use Ailos\Sdk\Http\Environment;

readonly class RegisterPayerEndpoint
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private Environment $environment,
        private AuthOrchestrator $authOrchestrator
    ) {
    }

    public function execute(Pagador $payer): void
    {
        try {
            $response = $this->httpClient->post(
                url: $this->environment->cadastrarPagadorUrl(),
                headers: [
                    'x-ailos-authentication' => $this->authOrchestrator->getJwtToken(),
                    'Authorization' => 'Bearer ' . $this->authOrchestrator->getAccessToken(),
                ],
                body: $payer->toArray()
            );
        } catch (\Throwable $exception) {
            throw AilosSdkException::withMessage($exception->getMessage());
        }

        $status = isset($response['_status_code']) && is_int($response['_status_code'])
            ? $response['_status_code']
            : 0;

        if ($status >= 400) {
            throw AilosSdkException::withMessage(
                'Unexpected response status: ' . (string) $status
            );
        }
    }
}
