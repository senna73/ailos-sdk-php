<?php

declare(strict_types=1);

namespace Ailos\Sdk\Http;

use Curl\Curl;
use RuntimeException;

final class CurlHttp implements IHttp
{
    public function get(Request $request): Response
    {
        $curl = $this->newCurl($request);
        $curl->get($request->path, $request->query);

        return $this->handleResponse($curl);
    }

    public function post(Request $request): Response
    {
        $curl = $this->newCurl($request);
        $curl->post($request->path, $request->body);

        return $this->handleResponse($curl);
    }

    public function put(Request $request): Response
    {
        $curl = $this->newCurl($request);
        $curl->put($request->path, $request->body);

        return $this->handleResponse($curl);
    }

    private function newCurl(Request $request): Curl
    {
        $curl = new Curl();

        $curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $curl->setOpt(CURLOPT_MAXREDIRS, 5);

        foreach ($request->headers as $name => $value) {
            $curl->setHeader($name, $value);
        }

        return $curl;
    }

    private function handleResponse(Curl $curl): Response
    {
        if ($curl->error) {
            throw new RuntimeException(
                // @phpstan-ignore-next-line argument.type
                sprintf(
                    'Falha ao comunicar com a API Ailos: [%s] %s',
                    $curl->errorCode,
                    $curl->errorMessage,
                ),
            );
        }

        if ($curl->httpStatusCode >= 400) {
            throw new RuntimeException(
                // @phpstan-ignore-next-line argument.type
                sprintf(
                    'API Ailos retornou erro HTTP %d: %s',
                    $curl->httpStatusCode,
                    $this->extractErrorMessage($curl->rawResponse),
                ),
            );
        }

        return new Response($curl->httpStatusCode, $curl->rawResponse);
    }

    private function extractErrorMessage(string $rawBody): string
    {
        if (trim($rawBody) === '') {
            return 'Erro desconhecido';
        }

        try {
            $decoded = json_decode($rawBody, associative: true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return $rawBody;
        }

        if (!is_array($decoded)) {
            return $rawBody;
        }

        $message = $decoded['message'] ?? $decoded['erro'] ?? $decoded['error'] ?? null;

        return is_string($message) ? $message : $rawBody;
    }
}
