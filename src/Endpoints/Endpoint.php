<?php

declare(strict_types=1);

namespace Ailos\Sdk\Endpoints;

use Ailos\Sdk\Config\AilosContext;
use Ailos\Sdk\Http\AuthenticatedHttp;
use Ailos\Sdk\Http\IHttp;
use Ailos\Sdk\Http\Request;
use Ailos\Sdk\Http\Response;

abstract class Endpoint
{
    private AuthenticatedHttp $http;

    public function __construct(
        public readonly AilosContext $context
    ) {
        $this->http = new AuthenticatedHttp($context);
    }

    protected function get(Request $request): Response
    {
        return $this->http->get($request);
    }

    protected function post(Request $request): Response
    {
        return $this->http->post($request);
    }

    protected function put(Request $request): Response
    {
        return $this->http->put($request);
    }
}
