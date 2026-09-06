<?php

declare(strict_types=1);

namespace Ailos\Sdk\Endpoints\Boleto;

use Ailos\Sdk\Endpoints\Endpoint;
use Ailos\Sdk\Http\Request;

final class GerarLoteCarne extends Endpoint
{
    /**
     * @param array<string, mixed> $lote
     */
    public function handle(string $convenio, array $lote): void
    {
        $this->post(new Request(
            path: "/ailos/cobranca/api/v2/boletos/gerar/carne/lote/convenios/{$convenio}",
            body: $lote
        ));
    }
}
