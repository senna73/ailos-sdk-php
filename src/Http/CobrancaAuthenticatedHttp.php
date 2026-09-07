<?php

declare(strict_types=1);

namespace Ailos\Sdk\Http;

use Ailos\Sdk\Cobranca\Auth\Auth;
use Ailos\Sdk\Config\AilosContext;

final class CobrancaAuthenticatedHttp implements IHttp
{
    public function __construct(private readonly AilosContext $context)
    {
    }

    public function get(Request $request): Response
    {
        return $this->context->config->http->get($this->authenticate($request));
    }

    public function post(Request $request): Response
    {
        return $this->context->config->http->post($this->authenticate($request));
    }

    public function put(Request $request): Response
    {
        return $this->context->config->http->put($this->authenticate($request));
    }

    public function delete(Request $request): Response
    {
        return $this->context->config->http->delete($this->authenticate($request));
    }

    private function authenticate(Request $request): Request
    {
        $request = $request->withPath(
            $this->context->environment->baseUrl . $request->path
        );

        $auth = new Auth($this->context);

        $auth->auth();

        $jwt = $auth->getJwt();

        if ($jwt === null) {
            throw new \RuntimeException('JWT não encontrado.');
        }

        $accessToken = $auth->getAccessToken();

        if ($accessToken === null) {
            throw new \RuntimeException('Access token não encontrado.');
        }

        // $token = $this->authManager->getValidToken();

        return $request->withHeaders([
            'Content-Type' => 'application/json',
            'accept' => 'application/json',
            'x-ailos-authentication' => 'Bearer ' . $jwt->code,
            'Authorization' => $accessToken->tokenType . ' ' . $accessToken->accessToken,
        ]);
    }
}
