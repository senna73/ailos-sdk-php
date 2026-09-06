<?php

declare(strict_types=1);

namespace Ailos\Sdk;

use Ailos\Sdk\Auth\Auth;
use Ailos\Sdk\Auth\Jwt;
use Ailos\Sdk\Config\AilosContext;
use Ailos\Sdk\Endpoints\Boleto\ConsultarBoleto;
use Ailos\Sdk\Http\AuthenticatedHttp;
use Ailos\Sdk\Http\IHttp;

final class Ailos
{
    private readonly IHttp $http;

    public function __construct(private AilosContext $context)
    {
        $this->http = new AuthenticatedHttp($this->context);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function handleJwtCallback(array $payload): void
    {
        $auth = new Auth($this->context);

        if ($payload['state'] == null) {
            throw new \RuntimeException('State não encontrado no JWT.');
        }

        if ($payload['state'] !== $auth->getState()) {
            throw new \RuntimeException('State inválido no JWT.');
        }

        $jwt = Jwt::fromArray($payload);

        $auth->setJwt($jwt);
    }

    /**
     * @return array<string, mixed>
     */
    public function consultarBoleto(string $convenio, string $numero): array
    {
        return new ConsultarBoleto($this->context, $this->http)->handle($convenio, $numero);
    }
}
