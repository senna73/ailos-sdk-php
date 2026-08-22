<?php

declare(strict_types=1);

namespace Ailos\Sdk\Entities;

use Ailos\Sdk\Framework\Entity;

class Documento extends Entity
{
    public function __construct(
        public readonly int $numeroDocumento,
        public readonly string $descricaoDocumento,
        public readonly int $especieDocumento
    ) {
    }
}
