<?php

declare(strict_types=1);

namespace Ailos\Sdk\Collections;

use Ailos\Sdk\Entities\Pagador;
use Ailos\Sdk\Framework\Collection;

readonly class PagadorCollection extends Collection
{
    public function cadastrarPagador(Pagador $pagador): void
    {
        $this->post(
            '/ailos/cobranca/api/v1/pagadores/cadastrar',
            $pagador
        );
    }

    public function alterarPagador(Pagador $pagador): void
    {
        $this->put(
            '/ailos/cobranca/api/v1/pagadores/alterar',
            $pagador
        );
    }
}
