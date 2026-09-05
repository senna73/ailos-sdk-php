<?php

declare(strict_types=1);

namespace Ailos\Sdk\Framework\HttpClient;

use Curl\Curl;
use RuntimeException;

readonly class CurlHttpClient implements IHttpClient
{
    private Curl $curl;

    public function __construct()
    {
        $this->curl = new Curl(null, [
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
        ]);
    }

    /**
     * @param array<mixed, mixed> $headers
     * @param array<mixed, mixed> $data
     * @throws RuntimeException
     */
    public function post(string $url, array $headers = [], array $data = []): HttpResponse
    {
        foreach ($headers as $name => $value) {
            $this->curl->setHeader($name, $value);
        }

        $this->curl->post($url, $data);

        return $this->buildResponse();
    }

    /**
     * @param array<mixed, mixed> $headers
     * @param array<mixed, mixed> $query
     * @throws RuntimeException
     */
    public function get(string $url, array $headers = [], array $query = []): HttpResponse
    {
        foreach ($headers as $name => $value) {
            $this->curl->setHeader($name, $value);
        }

        $this->curl->get($url, $query);

        return $this->buildResponse();
    }

    /**
     * @throws RuntimeException
     */
    private function buildResponse(): HttpResponse
    {
        if ($this->curl->error) {
            if (is_string($this->curl->errorMessage)) {
                throw new RuntimeException($this->curl->errorMessage);
            }

            throw new RuntimeException('Erro desconhecido no cliente Http');
        }

        /** @var string $rawResponse */
        $rawResponse = $this->curl->rawResponse ?? '';

        /** @var int $statusCode */
        $statusCode = $this->curl->httpStatusCode ?? 0;

        /** @var array<string, mixed> $responseHeaders */
        $responseHeaders = is_array($this->curl->responseHeaders)
            ? $this->curl->responseHeaders
            : [];

        return new HttpResponse(
            $statusCode,
            $rawResponse,
            $responseHeaders
        );
    }
}
