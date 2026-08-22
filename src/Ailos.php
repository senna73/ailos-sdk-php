<?php

declare(strict_types=1);

namespace Ailos\Sdk;

use Ailos\Sdk\Collections\BoletoCollection;
use Ailos\Sdk\Entities\Boleto;
use Ailos\Sdk\Entities\Enviroment;
use Ailos\Sdk\Entities\Jwt;
use Ailos\Sdk\Framework\Storage;

class Ailos
{
    public function __construct(private readonly Enviroment $enviroment)
    {
    }

    public static function handleJwtCallback(\stdClass $payload): void
    {
        $storage = Storage::storage();

        $jwt = Jwt::fromObject($payload);

        $item = $storage->getItem('jwt');
        $item->set($jwt);
        $item->expiresAfter(3600);
        $storage->save($item);
    }

    public function consultarUnicoBoleto(string $convenio, string $numero): Boleto
    {
        return new BoletoCollection($this->enviroment)->consultarUnicoBoleto($convenio, $numero);
    }
}
