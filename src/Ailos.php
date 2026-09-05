<?php

declare(strict_types=1);

namespace Ailos\Sdk;

use Ailos\Sdk\Collections\BoletoCollection;
use Ailos\Sdk\Entities\Boleto;
use Ailos\Sdk\Entities\Enviroment;
use Ailos\Sdk\Entities\Jwt;
use Ailos\Sdk\Entities\SdkConfig;
use Ailos\Sdk\Framework\Storage\ApcuStorage;

class Ailos
{
    public function __construct(
        private readonly Enviroment $enviroment,
        private readonly SdkConfig $config
    ) {
    }

    public static function handleJwtCallback(\stdClass $payload): void
    {
        $storage = new ApcuStorage();

        $jwt = Jwt::fromObject($payload);

        if ($jwt->state == null) {
            throw new \RuntimeException('State não encontrado no JWT.');
        }

        if ($storage->get('state') !== $jwt->state) {
            throw new \RuntimeException('State inválido no JWT.');
        }

        $storage->set('jwt', $jwt, 3600);
    }

    public function consultarBoleto(string $convenio, string $numero): Boleto
    {
        return new BoletoCollection($this->enviroment, $this->config)->consultarBoleto($convenio, $numero);
    }
}
