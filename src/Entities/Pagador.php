<?php

declare(strict_types=1);

namespace Ailos\Sdk\Entities;

use Ailos\Sdk\Framework\Entity;

class Pagador extends Entity
{
    /**
     * @param list<array{endereco: string}> $emails
     * @param list<string> $mensagemPagador
     */
    public function __construct(
        public readonly EntidadeLegal $entidadeLegal,
        public readonly Telefone $telefone,
        public readonly array $emails,
        public readonly Endereco $endereco,
        public readonly array $mensagemPagador,
        public readonly bool $dda
    ) {
    }
}
