<?php

declare(strict_types=1);

namespace Ailos\Sdk\Endpoints\Cobranca\Pagador;

use Ailos\Sdk\Endpoints\Endpoint;
use Ailos\Sdk\Http\Request;

/**
 * @phpstan-type AlterarPagadorRequest array{
 *     pagador: array{
 *         entidadeLegal: array{
 *             identificadorReceitaFederal: string,
 *             tipoPessoa: int,
 *             nome: string
 *         },
 *         telefone: array{
 *             ddi: string,
 *             ddd: string,
 *             numero: string
 *         },
 *         emails: list<array{
 *             endereco: string
 *         }>,
 *         endereco: array{
 *             cep: string,
 *             logradouro: string,
 *             numero: string,
 *             complemento: string,
 *             bairro: string,
 *             cidade: string,
 *             uf: string
 *         },
 *         mensagemPagador: list<string>,
 *         dda: bool
 *     }
 * }
 */
final class AlterarPagador extends Endpoint
{
    /**
    * @param AlterarPagadorRequest $pagador
     */
    public function handle(array $pagador): void
    {
        $this->put(new Request(
            path: '/ailos/cobranca/api/v1/pagadores/alterar',
            body: $pagador
        ));
    }
}
