<?php

declare(strict_types=1);

namespace Ailos\Sdk\Http\Contracts;

interface HttpClientInterface
{
    public function get(string $url, array $headers = []): array;

    public function post(string $url, array $headers = [], array $body = []): array;

    public function postForm(string $url, array $headers = [], array $fields = []): array;

    public function postUrlEncoded(string $url, array $headers = [], string $body = ''): array;
}
