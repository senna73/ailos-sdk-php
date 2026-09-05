<?php

declare(strict_types=1);

namespace Ailos\Sdk\Entities;

use Ailos\Sdk\Framework\Entity;

class BoletoCarneLote extends Entity
{
    /**
     * @param BoletoCarne[] $carnes
     */
    public function __construct(
        public readonly ConvenioCobranca $convenioCobranca,
        public readonly array $carnes,
    ) {
    }
}
