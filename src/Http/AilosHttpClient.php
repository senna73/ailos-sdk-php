<?php

declare(strict_types=1);

namespace Ailos\Sdk\Http;

use Ailos\Sdk\Exceptions\HttpException;
use Ailos\Sdk\Http\Contracts\HttpClientInterface;
use JsonException;

class AilosHttpClient implements HttpClientInterface
{
    private const int TIMEOUT_SECONDS = 30;

    /**
     * @param array<string, string> $headers
     * @return array<string, mixed>
     */
    public function get(string $url, array $headers = []): array
    {
        return $this->request($url, $headers, [
            CURLOPT_HTTPGET => true,
        ]);
    }

    /**
     * @param array<string, string> $headers
     * @param array<string, mixed> $body
     * @return array<string, mixed>
     * @throws JsonException
     */
    public function post(string $url, array $headers = [], array $body = []): array
    {
        $encoded = json_encode($body, JSON_THROW_ON_ERROR);

        return $this->request($url, array_merge($headers, ['Content-Type' => 'application/json']), [
            CURLOPT_POST       => true,
            CURLOPT_POSTFIELDS => $encoded,
        ]);
    }

    /**
     * @param array<string, string> $headers
     * @param array<string, mixed>  $fields
     * @return array<string, mixed>
     */
    public function postForm(string $url, array $headers = [], array $fields = []): array
    {
        return $this->request($url, $headers, [
            CURLOPT_POST       => true,
            CURLOPT_POSTFIELDS => $fields,
        ]);
    }

    /**
     * @param array<string, string> $headers
     * @return array<string, mixed>
     */
    public function postUrlEncoded(string $url, array $headers = [], string $body = ''): array
    {
        return $this->request($url, array_merge($headers, ['Content-Type' => 'application/x-www-form-urlencoded']), [
            CURLOPT_POST       => true,
            CURLOPT_POSTFIELDS => $body,
        ]);
    }

    /**
     * @param array<string, string> $headers
     * @param array<int, mixed>     $curlOptions
     * @return array<string, mixed>
     */
    private function request(string $url, array $headers, array $curlOptions): array
    {
        $curl = $this->initCurl($url, $headers, $curlOptions);

        try {
            return $this->execute($curl, $url);
        } finally {
            curl_close($curl);
        }
    }

    /**
     * @param array<string, string> $headers
     * @param array<int, mixed>     $curlOptions
     */
    private function initCurl(string $url, array $headers, array $curlOptions): \CurlHandle
    {
        $curl = curl_init($url);

        if ($curl === false) {
            throw HttpException::fromConnectionFailure("Failed to initialize cURL for URL: {$url}");
        }

        $normalizedHeaders = array_map(
            static fn (string $key, string $value): string => "{$key}: {$value}",
            array_keys($headers),
            array_values($headers),
        );

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => self::TIMEOUT_SECONDS,
            CURLOPT_HTTPHEADER     => $normalizedHeaders,
            CURLOPT_FOLLOWLOCATION => true,
        ];

        foreach ($curlOptions as $key => $value) {
            $options[$key] = $value;
        }

        curl_setopt_array($curl, $options);

        return $curl;
    }

    /**
     * @return array<string, mixed>
     */
    private function execute(\CurlHandle $curl, string $url): array
    {
        $raw = curl_exec($curl);

        if (!is_string($raw)) {
            $error = curl_error($curl);

            throw HttpException::fromConnectionFailure(
                $error !== '' ? $error : "Unknown cURL error for URL: {$url}"
            );
        }

        $statusCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($statusCode >= 400) {
            throw HttpException::fromResponse($statusCode, $raw);
        }

        $decoded = json_decode($raw, true);

        if (is_array($decoded)) {
            /** @var array<string, mixed> $decoded */
            return $decoded + ['_status_code' => $statusCode];
        }

        return [
            'raw' => $raw,
            '_status_code' => $statusCode,
        ];
    }
}
