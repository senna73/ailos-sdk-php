<?php

declare(strict_types=1);

namespace Ailos\Sdk\Entities;

use Ailos\Sdk\Framework\Entity;

class TipoVencimento extends Entity
{
    public function __construct(
        public readonly int $tipoVencimento,
        public readonly int $quantidadeXDias,
        public readonly int $diaXDeCadaMes,
    ) {
    }
}
