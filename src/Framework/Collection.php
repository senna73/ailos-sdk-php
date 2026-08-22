<?php

declare(strict_types=1);

namespace Ailos\Sdk\Framework;

use Ailos\Sdk\Entities\Enviroment;

abstract readonly class Collection
{
    private HttpClient $httpClient;
    private AuthManager $authManager;

    public function __construct(private Enviroment $enviroment)
    {
        $this->httpClient = new HttpClient();
        $this->authManager = new AuthManager($this->enviroment, $this->httpClient);
    }

    /**
     * @param array<string, mixed> $headers
     */
    protected function post(string $url, ?Entity $data, array $headers = []): mixed
    {
        $this->authManager->auth();

        $url = $this->enviroment->baseUrl . $url;
        $headers = array_merge($this->authManager->getAuthHeader(), $headers);

        if ($data === null) {
            $data = [];
        } else {
            $data = $data::toArray();
        }

        return $this->httpClient->post(
            $url,
            $headers,
            $data
        );
    }

    /**
     * @param array<string, mixed> $headers
     * @param array<string, mixed> $query
     */
    protected function get(string $url, array $query = [], array $headers = []): mixed
    {
        $this->authManager->auth();

        $url = $this->enviroment->baseUrl . $url;
        $headers = array_merge($this->authManager->getAuthHeader(), $headers);

        return $this->httpClient->get(
            $url,
            $headers,
            $query
        );
    }
}
