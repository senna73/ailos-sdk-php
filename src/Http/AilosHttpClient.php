<?php

declare(strict_types=1);

namespace Ailos\Sdk\Http;

use Ailos\Sdk\Exceptions\HttpException;
use Ailos\Sdk\Http\Contracts\HttpClientInterface;

class AilosHttpClient implements HttpClientInterface
{
    private const TIMEOUT_SECONDS = 30;

    public function get(string $url, array $headers = []): array
    {
        $curl = $this->createCurl($url, $headers);

        curl_setopt($curl, CURLOPT_HTTPGET, true);

        return $this->execute($curl, $url);
    }

    public function post(string $url, array $headers = [], array $body = []): array
    {
        $curl = $this->createCurl($url, array_merge($headers, [
            'Content-Type: application/json',
        ]));

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body));

        return $this->execute($curl, $url);
    }

    public function postForm(string $url, array $headers = [], array $fields = []): array
    {
        $curl = $this->createCurl($url, $headers);

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);

        return $this->execute($curl, $url);
    }

    public function postUrlEncoded(string $url, array $headers = [], string $body = ''): array
    {
        $curl = $this->createCurl($url, array_merge($headers, [
            'Content-Type: application/x-www-form-urlencoded',
        ]));

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

        return $this->execute($curl, $url);
    }

    private function createCurl(string $url, array $headers): \CurlHandle
    {
        $curl = curl_init($url);

        if ($curl === false) {
            throw HttpException::fromConnectionFailure("Failed to initialize cURL for URL: {$url}");
        }

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => self::TIMEOUT_SECONDS,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_FOLLOWLOCATION => true,
        ]);

        return $curl;
    }

    private function execute(\CurlHandle $curl, string $url): array
    {
        $response   = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error      = curl_error($curl);

        curl_close($curl);

        if ($response === false || !empty($error)) {
            throw HttpException::fromConnectionFailure($error ?: "Unknown cURL error for URL: {$url}");
        }

        $decoded = json_decode((string) $response, associative: true);

        if (!is_array($decoded)) {
            $decoded = ['raw' => $response];
        }

        if ($statusCode >= 400) {
            throw HttpException::fromResponse($statusCode, (string) $response);
        }

        return array_merge($decoded, ['_status_code' => $statusCode]);
    }
}
