<?php

declare(strict_types=1);

namespace Ailos\Sdk\Collections;

use Ailos\Sdk\Entities\Boleto;
use Ailos\Sdk\Entities\BoletoLote;
use Ailos\Sdk\Framework\Collection;
use DomainException;

readonly class BoletoCollection extends Collection
{
    public function consultarUnicoBoleto(string $convenio, string $numero): Boleto
    {
        $response = $this->get(
            "/ailos/cobranca/api/v2/boletos/consultar/boleto/convenios/{$convenio}/{$numero}"
        );

        if (!($response instanceof \stdClass)) {
            throw new DomainException('Tipo de retorno incorreto');
        }

        return Boleto::fromObject($response);
    }

    public function gerarUnicoBoleto(string $convenio, Boleto $boleto): void
    {
        $this->post(
            "/ailos/cobranca/api/v2/boletos/gerar/boleto/convenios/{$convenio}",
            $boleto
        );
    }

    public function gerarLoteBoletos(string $convenio, BoletoLote $lote): void
    {
        $this->post(
            "/ailos/cobranca/api/v2/boletos/gerar/lote/convenios/{$convenio}",
            $lote
        );
    }


}
