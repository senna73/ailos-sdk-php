<?php

declare(strict_types=1);

namespace Ailos\Sdk\Http;

use Ailos\Sdk\Config\AilosContext;
use Ailos\Sdk\Http\IHttp;
use Ailos\Sdk\Http\Request;
use Ailos\Sdk\Http\Response;

abstract class Endpoint
{

    public function __construct(
        public readonly AilosContext $context,
        public readonly IHttp $http
    ) {
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

    protected function delete(Request $request): Response
    {
        return $this->http->delete($request);
    }
}
