<?php

declare(strict_types=1);

namespace Ailos\Sdk\Endpoints\Pagador;

use Ailos\Sdk\Endpoints\Endpoint;
use Ailos\Sdk\Http\Request;

final class ListarPagadores extends Endpoint
{
    /**
     * @return array<string, mixed>
     */
    public function handle(): array
    {
        $response = $this->get(new Request(
            path: '/ailos/cobranca/api/v1/pagadores/listar'
        ));

        return $response->json();
    }
}
