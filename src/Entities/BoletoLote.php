<?php

declare(strict_types=1);

namespace Ailos\Sdk\Entities;

use Ailos\Sdk\Framework\Entity;

class BoletoLote extends Entity
{
    /**
     * @param Boleto[] $boletos
     */
    public function __construct(
        public readonly ConvenioCobranca $convenioCobranca,
        public readonly array $boletos,
    ) {
    }
}
