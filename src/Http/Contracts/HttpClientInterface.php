<?php

declare(strict_types=1);

namespace Ailos\Sdk\Http\Contracts;

interface HttpClientInterface
{
    /**
     * @param array<string, string> $headers
     * @return array<string, mixed>
     */
    public function get(string $url, array $headers = []): array;

    /**
     * @param array<string, string> $headers
     * @param array<string, mixed> $body
     * @return array<string, mixed>
     */
    public function post(string $url, array $headers = [], array $body = []): array;

    /**
     * @param array<string, string> $headers
     * @param array<string, mixed> $fields
     * @return array<string, mixed>
     */
    public function postForm(string $url, array $headers = [], array $fields = []): array;

    /**
     * @param array<string, string> $headers
     * @return array<string, mixed>
     */
    public function postUrlEncoded(string $url, array $headers = [], string $body = ''): array;
}
