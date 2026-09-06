<?php

declare(strict_types=1);

namespace Ailos\Sdk\Endpoints\Boleto;

use Ailos\Sdk\Endpoints\Endpoint;
use Ailos\Sdk\Http\Request;

final class GerarBoleto extends Endpoint
{
    /**
     * @param array<string, mixed> $boleto
     */
    public function handle(string $convenio, array $boleto): void
    {
        $this->post(new Request(
            path: "/ailos/cobranca/api/v2/boletos/gerar/boleto/convenios/{$convenio}",
            body: $boleto
        ));
    }
}
