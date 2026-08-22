<?php

declare(strict_types=1);

namespace Ailos\Sdk\Entities;

use Ailos\Sdk\Framework\Entity;

class Telefone extends Entity
{
    public function __construct(
        public readonly string $ddi,
        public readonly string $ddd,
        public readonly string $numero
    ) {
    }
}
