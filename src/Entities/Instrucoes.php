<?php

declare(strict_types=1);

namespace Ailos\Sdk\Entities;

use Ailos\Sdk\Framework\Entity;

class Instrucoes extends Entity
{
    /**
     * @param list<array{valor: float, diasAteVencimento: int}> $descontos
     */
    public function __construct(
        public readonly float $valorAbatimento,
        public readonly int $tipoDesconto,
        public readonly array $descontos,
        public readonly int $tipoMulta,
        public readonly float $valorMulta,
        public readonly int $tipoJurosMora,
        public readonly float $valorJurosMora,
        public readonly int $diasNegativacao,
        public readonly int $diasProtesto
    ) {
    }
}
