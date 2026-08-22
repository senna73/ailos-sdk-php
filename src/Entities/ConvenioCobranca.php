<?php

declare(strict_types=1);

namespace Ailos\Sdk\Entities;

use Ailos\Sdk\Framework\Entity;

class ConvenioCobranca extends Entity
{
    public function __construct(
        public readonly int $codigoCarteiraCobranca
    ) {
    }
}
