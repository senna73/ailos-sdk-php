<?php

declare(strict_types=1);

namespace Ailos\Sdk\Cobranca\Endpoints\Pagador;

use use Ailos\Sdk\Cobranca\Endpoints\Endpoint;
use Ailos\Sdk\Http\Request;

/**
 * @phpstan-type ListarPagadoresResponse array{
 *     pagadorResponse: list<array{
 *         entidadeLegal: array{
 *             identificadorReceitaFederal: string,
 *             tipoPessoa: int,
 *             nome: string
 *         },
 *         telefone: array{
 *             ddi: string,
 *             ddd: string|null,
 *             numero: string
 *         },
 *         emails: list<array{
 *             endereco: string
 *         }>,
 *         endereco: array{
 *             cep: string,
 *             logradouro: string,
 *             numero: int,
 *             complemento: string|null,
 *             bairro: string,
 *             cidade: array{
 *                 codigo: int|null,
 *                 codigoMunicipioIbge: int,
 *                 nome: string,
 *                 uf: string
 *             }
 *         },
 *         mensagemPagador: list<string>|null,
 *         dda: bool,
 *         ativo: bool
 *     }>
 * }
 */
final class ListarPagadores extends Endpoint
{
    /**
     * @return ListarPagadoresResponse
     */
    public function handle(): array
    {
        $response = $this->get(new Request(
            path: '/ailos/cobranca/api/v1/pagadores/listar'
        ));

        /** @var ListarPagadoresResponse $body */
        $body = $response->json();

        return $body;
    }
}
