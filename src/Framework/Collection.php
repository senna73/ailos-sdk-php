<?php

declare(strict_types=1);

namespace Ailos\Sdk\Framework;

use Ailos\Sdk\Entities\Enviroment;
use Ailos\Sdk\Entities\SdkConfig;

abstract readonly class Collection
{
    private AuthManager $authManager;

    public function __construct(private Enviroment $enviroment, private SdkConfig $config)
    {
        $this->authManager = new AuthManager($this->enviroment, $this->config);
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

        return $this->config->http->post(
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

        return $this->config->http->get(
            $url,
            $headers,
            $query
        );
    }
}
