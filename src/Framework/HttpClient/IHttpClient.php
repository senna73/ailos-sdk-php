<?php

declare(strict_types=1);

namespace Ailos\Sdk\Framework\HttpClient;

use RuntimeException;

interface IHttpClient
{
    /**
     * @param array<mixed, mixed> $headers
     * @param array<mixed, mixed> $data
     * @throws RuntimeException
     */
    public function post(string $url, array $headers = [], array $data = []): HttpResponse;

    /**
     * @param array<mixed, mixed> $headers
     * @param array<mixed, mixed> $data
     * @throws RuntimeException
     */
    public function put(string $url, array $headers = [], array $data = []): HttpResponse;

    /**
     * @param array<mixed, mixed> $headers
     * @param array<mixed, mixed> $query
     * @throws RuntimeException
     */
    public function get(string $url, array $headers = [], array $query = []): HttpResponse;
}
