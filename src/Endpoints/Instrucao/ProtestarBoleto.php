<?php

declare(strict_types=1);

namespace Ailos\Sdk\Endpoints\Instrucao;

use Ailos\Sdk\Endpoints\Endpoint;
use Ailos\Sdk\Http\Request;

/**
 * @phpstan-type ProtestarBoletoRequest array{
 *     boletos: list<array{
 *         numeroConvenio: int,
 *         numeroBoleto: int
 *     }>
 * }
 */
final class ProtestarBoleto extends Endpoint
{
    /**
     * @param ProtestarBoletoRequest $instrucoes
     */
    public function handle(array $instrucoes): void
    {
        $this->post(new Request(
            path: '/ailos/cobranca/api/v1/boletos/protesto/lote',
            body: $instrucoes
        ));
    }
}
