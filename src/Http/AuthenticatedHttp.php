<?php

declare(strict_types=1);

namespace Ailos\Sdk\Http;

use Ailos\Sdk\Auth\Auth;
use Ailos\Sdk\Config\AilosContext;

final class AuthenticatedHttp implements IHttp
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

    private function authenticate(Request $request): Request
    {
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
            'x-ailos-authentication' => 'Bearer ' . $jwt->code,
            'Authorization' => $accessToken->tokenType . ' ' . $accessToken->accessToken,
        ]);
    }
}
