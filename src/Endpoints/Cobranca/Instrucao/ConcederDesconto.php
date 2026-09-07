<?php

declare(strict_types=1);

namespace Ailos\Sdk\Endpoints\Cobranca\Instrucao;

use Ailos\Sdk\Endpoints\Endpoint;
use Ailos\Sdk\Http\Request;

/**
 * @phpstan-type ConcederDescontoRequest array{
 *     boletos: list<array{
 *         numeroConvenio: int,
 *         numeroBoleto: int,
 *         tipoDesconto: int,
 *         descontos: list<array{
 *             valor: int,
 *             diasAteVencimento: int
 *         }>
 *     }>
 * }
 */
final class ConcederDesconto extends Endpoint
{
    /**
     * @param ConcederDescontoRequest $instrucoes
     */
    public function handle(array $instrucoes): void
    {
        $this->post(new Request(
            path: '/ailos/cobranca/api/v1/boletos/desconto/lote',
            body: $instrucoes
        ));
    }
}
