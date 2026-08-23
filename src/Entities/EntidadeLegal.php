<?php

declare(strict_types=1);

namespace Ailos\Sdk\Entities;

use Ailos\Sdk\Framework\Entity;

class EntidadeLegal extends Entity
{
    public function __construct(
        public readonly string $identificadorReceitaFederal,
        public readonly int $tipoPessoa,
        public readonly string $nome
    ) {
    }
}
