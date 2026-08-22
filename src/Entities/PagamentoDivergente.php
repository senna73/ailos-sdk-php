<?php

declare(strict_types=1);

namespace Ailos\Sdk\Entities;

use Ailos\Sdk\Framework\Entity;

class PagamentoDivergente extends Entity
{
    public function __construct(
        public readonly int $tipoPagamentoDivergente,
        public readonly int $valorMinimoPagamentoDivergente
    ) {
    }
}
