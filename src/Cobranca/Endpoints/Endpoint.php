<?php

declare(strict_types=1);

namespace Ailos\Sdk\Cobranca\Endpoints;

use Ailos\Sdk\Cobranca\Auth\Auth;
use Ailos\Sdk\Cobranca\Context\CobrancaContext;
use Ailos\Sdk\Http\IHttp;
use Ailos\Sdk\Http\Request;
use Ailos\Sdk\Http\Response;

abstract class Endpoint implements IHttp
{
    public function __construct(
        public readonly CobrancaContext $context,
    ) {
    }

    public function get(Request $request): Response
    {
        return $this->context->http->get($this->authenticate($request));
    }

    public function post(Request $request): Response
    {
        return $this->context->http->post($this->authenticate($request));
    }

    public function put(Request $request): Response
    {
        return $this->context->http->put($this->authenticate($request));
    }

    public function delete(Request $request): Response
    {
        return $this->context->http->delete($this->authenticate($request));
    }

    private function authenticate(Request $request): Request
    {
        $request = $request->withPath(
            $this->context->baseUrl . $request->path
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
