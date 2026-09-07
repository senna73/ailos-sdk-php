<?php

declare(strict_types=1);

namespace Ailos\Sdk\Endpoints\Cobranca\Instrucao;

use Ailos\Sdk\Endpoints\Endpoint;
use Ailos\Sdk\Http\Request;

/**
 * @phpstan-type ProtestoAutomaticoRequest array{
 *     boletos: list<array{
 *         numeroConvenio: int,
 *         numeroBoleto: int,
 *         diasProtesto: int
 *     }>
 * }
 */
final class ProtestoAutomatico extends Endpoint
{
    /**
     * @param ProtestoAutomaticoRequest $instrucoes
     */
    public function handle(array $instrucoes): void
    {
        $this->post(new Request(
            path: '/ailos/cobranca/api/v1/boletos/protesto-automatico/lote',
            body: $instrucoes
        ));
    }
}
