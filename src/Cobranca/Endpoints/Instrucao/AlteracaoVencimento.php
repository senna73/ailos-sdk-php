<?php

declare(strict_types=1);

namespace Ailos\Sdk\Cobranca\Endpoints\Instrucao;

use use Ailos\Sdk\Cobranca\Endpoints\Endpoint;
use Ailos\Sdk\Http\Request;

/**
 * @phpstan-type AlteracaoVencimentoRequest array{
 *     boletos: list<array{
 *         numeroConvenio: int,
 *         numeroBoleto: int,
 *         vencimento: array{
 *             dataVencimento: string
 *         }
 *     }>
 * }
 */
final class AlteracaoVencimento extends Endpoint
{
    /**
     * @param AlteracaoVencimentoRequest $instrucoes
     */
    public function handle(array $instrucoes): void
    {
        $this->post(new Request(
            path: '/ailos/cobranca/api/v1/boletos/vencimento/lote',
            body: $instrucoes
        ));
    }
}
