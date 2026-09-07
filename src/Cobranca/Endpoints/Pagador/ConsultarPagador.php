<?php

declare(strict_types=1);

namespace Ailos\Sdk\Cobranca\Endpoints\Pagador;

use Ailos\Sdk\Http\Endpoint;
use Ailos\Sdk\Http\Request;

/**
 * @phpstan-type ConsultarPagadorResponse array{
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
 *             complemento: string,
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
final class ConsultarPagador extends Endpoint
{
    /**
     * @return ConsultarPagadorResponse
     */
    public function handle(string $documento): array
    {
        $response = $this->get(new Request(
            path: "/ailos/cobranca/api/v1/pagadores/consultar/{$documento}"
        ));

        /** @var ConsultarPagadorResponse $body */
        $body = $response->json();

        return $body;
    }
}
