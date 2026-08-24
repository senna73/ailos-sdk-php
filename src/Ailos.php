<?php

declare(strict_types=1);

namespace Ailos\Sdk;

use Ailos\Sdk\Collections\BoletoCollection;
use Ailos\Sdk\Entities\Boleto;
use Ailos\Sdk\Entities\Enviroment;
use Ailos\Sdk\Entities\Jwt;
use Ailos\Sdk\Framework\Storage\FileStorage;

class Ailos
{
    public function __construct(private readonly Enviroment $enviroment)
    {
    }

    public static function handleJwtCallback(\stdClass $payload): void
    {
        $storage = new FileStorage();

        $jwt = Jwt::fromObject($payload);

        $storage->set('jwt', $jwt, 3600);
    }

    public function consultarUnicoBoleto(string $convenio, string $numero): Boleto
    {
        return new BoletoCollection($this->enviroment)->consultarUnicoBoleto($convenio, $numero);
    }
}
