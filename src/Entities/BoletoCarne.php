<?php

declare(strict_types=1);

namespace Ailos\Sdk\Entities;

use Ailos\Sdk\Framework\Entity;

class BoletoCarne extends Entity
{
    public function __construct(
        public readonly ConvenioCobranca $convenioCobranca,
        public readonly Documento $documento,
        public readonly Emissao $emissao,
        public readonly Pagador $pagador,
        public readonly Vencimento $vencimento,
        public readonly Instrucoes $instrucoes,
        public readonly ValorBoleto $valorBoleto,
        public readonly AvisoSms $avisoSms,
        public readonly PagamentoDivergente $pagamentoDivergente,
        public readonly Avalista $avalista,
        public readonly int $indicadorRegistroNuclea,
    ) {
    }
}
