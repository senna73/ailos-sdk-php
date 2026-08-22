<?php

declare(strict_types=1);

namespace Ailos\Sdk\Entities;

use Ailos\Sdk\Framework\Entity;

class Endereco extends Entity
{
    public function __construct(
        public readonly string $cep,
        public readonly string $logradouro,
        public readonly string $numero,
        public readonly string $complemento,
        public readonly string $bairro,
        public readonly string $cidade,
        public readonly string $uf
    ) {
    }
}
