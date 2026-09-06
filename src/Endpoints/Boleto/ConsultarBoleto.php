<?php

declare(strict_types=1);

namespace Ailos\Sdk\Endpoints\Boleto;

use Ailos\Sdk\Endpoints\Endpoint;
use Ailos\Sdk\Http\Request;

final class ConsultarBoleto extends Endpoint
{
    /**
     * @return array<string, mixed>
     */
    public function handle(string $convenio, string $numero): array
    {
        $response = $this->get(new Request(
            path: "/ailos/cobranca/api/v2/boletos/consultar/boleto/convenios/{$convenio}/{$numero}"
        ));

        return $response->json();
    }
}
