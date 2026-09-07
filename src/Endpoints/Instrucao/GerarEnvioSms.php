<?php

declare(strict_types=1);

namespace Ailos\Sdk\Endpoints\Instrucao;

use Ailos\Sdk\Endpoints\Endpoint;
use Ailos\Sdk\Http\Request;

/**
 * @phpstan-type GerarEnvioSmsRequest array{
 *     boletos: list<array{
 *         numeroConvenio: int,
 *         numeroBoleto: int,
 *         tipoEnvio: int,
 *         telefone: array{
 *             ddi: string,
 *             ddd: string,
 *             numero: string
 *         }
 *     }>
 * }
 */
final class GerarEnvioSms extends Endpoint
{
    /**
     * @param GerarEnvioSmsRequest $instrucoes
     */
    public function handle(array $instrucoes): void
    {
        $this->post(new Request(
            path: '/ailos/cobranca/api/v1/boletos/vencimento/lote',
            body: $instrucoes
        ));
    }
}
