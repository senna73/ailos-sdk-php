<?php

declare(strict_types=1);

namespace Ailos\Sdk\Endpoints\Cobranca\Instrucao;

use Ailos\Sdk\Endpoints\Endpoint;
use Ailos\Sdk\Http\Request;

/**
 * @phpstan-type BaixaRequest array{
 *     boletos: list<array{
 *         numeroConvenio: int,
 *         numeroBoleto: int
 *     }>
 * }
 */
final class Baixa extends Endpoint
{
    /**
     * @param BaixaRequest $instrucoes
     */
    public function handle(array $instrucoes): void
    {
        $this->delete(new Request(
            path: '/ailos/cobranca/api/v1/boletos/lote',
            body: $instrucoes
        ));
    }
}
