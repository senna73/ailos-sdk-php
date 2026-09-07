<?php

declare(strict_types=1);

namespace Ailos\Sdk\Endpoints\Cobranca\Instrucao;

use Ailos\Sdk\Endpoints\Endpoint;
use Ailos\Sdk\Http\Request;

/**
 * @phpstan-type CancelarAbatimentoRequest array{
 *     boletos: list<array{
 *         numeroConvenio: int,
 *         numeroBoleto: int
 *     }>
 * }
 */
final class CancelarAbatimento extends Endpoint
{
    /**
     * @param CancelarAbatimentoRequest $instrucoes
     */
    public function handle(array $instrucoes): void
    {
        $this->delete(new Request(
            path: '/ailos/cobranca/api/v1/boletos/abatimento/lote',
            body: $instrucoes
        ));
    }
}
