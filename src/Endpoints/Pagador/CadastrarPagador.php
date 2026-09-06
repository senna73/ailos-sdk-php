<?php

declare(strict_types=1);

namespace Ailos\Sdk\Endpoints\Pagador;

use Ailos\Sdk\Endpoints\Endpoint;
use Ailos\Sdk\Http\Request;

final class CadastrarPagador extends Endpoint
{
    /**
     * @param array<string, mixed> $pagador
     */
    public function handle(array $pagador): void
    {
        $this->post(new Request(
            path: '/ailos/cobranca/api/v1/pagadores/cadastrar',
            body: $pagador
        ));
    }
}
