<?php

declare(strict_types=1);

namespace Ailos\Sdk\Cobranca\Endpoints\Instrucao;

use Ailos\Sdk\Http\Endpoint;
use Ailos\Sdk\Http\Request;

/**
 * @phpstan-type ConcederAbatimentoRequest array{
 *     boletos: list<array{
 *         numeroConvenio: int,
 *         numeroBoleto: int,
 *         valorAbatimento: int
 *     }>
 * }
 */
final class ConcederAbatimento extends Endpoint
{
    /**
     * @param ConcederAbatimentoRequest $instrucoes
     */
    public function handle(array $instrucoes): void
    {
        $this->post(new Request(
            path: '/ailos/cobranca/api/v1/boletos/abatimento/lote',
            body: $instrucoes
        ));
    }
}
