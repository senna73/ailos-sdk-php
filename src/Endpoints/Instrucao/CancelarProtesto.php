<?php

declare(strict_types=1);

namespace Ailos\Sdk\Endpoints\Instrucao;

use Ailos\Sdk\Endpoints\Endpoint;
use Ailos\Sdk\Http\Request;

/**
 * @phpstan-type CancelarProtestoRequest array{
 *     boletos: list<array{
 *         numeroConvenio: int,
 *         numeroBoleto: int
 *     }>
 * }
 */
final class CancelarProtesto extends Endpoint
{
    /**
     * @param CancelarProtestoRequest $instrucoes
     */
    public function handle(array $instrucoes): void
    {
        $this->delete(new Request(
            path: '/ailos/cobranca/api/v1/boletos/lote',
            body: $instrucoes
        ));
    }
}
