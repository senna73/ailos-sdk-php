<?php

declare(strict_types=1);

namespace Ailos\Sdk\Cobranca\Endpoints\Instrucao;

use Ailos\Sdk\Http\Endpoint;
use Ailos\Sdk\Http\Request;

/**
 * @phpstan-type AnuenciaEletronicaRequest array{
 *     boletos: list<array{
 *         numeroConvenio: int,
 *         numeroBoleto: int
 *     }>
 * }
 */
final class AnuenciaEletronica extends Endpoint
{
    /**
     * @param AnuenciaEletronicaRequest $instrucoes
     */
    public function handle(array $instrucoes): void
    {
        $this->post(new Request(
            path: '/ailos/cobranca/api/v1/boletos/anuencia-eletronica/lote',
            body: $instrucoes
        ));
    }
}
