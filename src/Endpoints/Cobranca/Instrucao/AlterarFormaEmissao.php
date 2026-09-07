<?php

declare(strict_types=1);

namespace Ailos\Sdk\Endpoints\Cobranca\Instrucao;

use Ailos\Sdk\Endpoints\Endpoint;
use Ailos\Sdk\Http\Request;

/**
 * @phpstan-type AlterarFormaEmissaoRequest array{
 *     boletos: list<array{
 *         numeroConvenio: int,
 *         numeroBoleto: int,
 *         emissao: array{
 *             forma: int
 *         }
 *     }>
 * }
 */
final class AlterarFormaEmissao extends Endpoint
{
    /**
     * @param AlterarFormaEmissaoRequest $instrucoes
     */
    public function handle(array $instrucoes): void
    {
        $this->post(new Request(
            path: '/ailos/cobranca/api/v1/boletos/forma-emissao/lote',
            body: $instrucoes
        ));
    }
}
