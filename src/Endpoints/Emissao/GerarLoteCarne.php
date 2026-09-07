<?php

declare(strict_types=1);

namespace Ailos\Sdk\Endpoints\Emissao;

use Ailos\Sdk\Endpoints\Endpoint;
use Ailos\Sdk\Http\Request;

/**
 * @phpstan-type GerarLoteCarneRequest array{
 *     convenioCobranca: array{
 *         codigoCarteiraCobranca: int
 *     },
 *     carnes: list<array{
 *         convenioCobranca: array{
 *             codigoCarteiraCobranca: int
 *         },
 *         documento: array{
 *             numeroDocumento: int,
 *             descricaoDocumento: string,
 *             especieDocumento: int
 *         },
 *         emissao: array{
 *             formaEmissao: int,
 *             dataEmissaoDocumento: string
 *         },
 *         pagador: array{
 *             entidadeLegal: array{
 *                 identificadorReceitaFederal: string,
 *                 tipoPessoa: int,
 *                 nome: string
 *             },
 *             telefone: array{
 *                 ddi: string,
 *                 ddd: string,
 *                 numero: string
 *             },
 *             emails: list<array{
 *                 endereco: string
 *             }>,
 *             endereco: array{
 *                 cep: string,
 *                 logradouro: string,
 *                 numero: string,
 *                 complemento: string,
 *                 bairro: string,
 *                 cidade: string,
 *                 uf: string
 *             },
 *             mensagemPagador: list<string>
 *         },
 *         vencimento: array{
 *             dataVencimento: string
 *         },
 *         instrucoes: array{
 *             valorAbatimento: int,
 *             tipoDesconto: int,
 *             descontos: list<array{
 *                 valor: int,
 *                 diasAteVencimento: int
 *             }>,
 *             tipoMulta: int,
 *             valorMulta: int,
 *             tipoJurosMora: int,
 *             valorJurosMora: int,
 *             diasNegativacao: int,
 *             diasProtesto: int
 *         },
 *         valorBoleto: array{
 *             valorNominal: int
 *         },
 *         avisoSms: array{
 *             enviarAvisoVencimentoSms: int,
 *             enviarAvisoVencimentoSmsAntesVencimento: bool,
 *             enviarAvisoVencimentoSmsDiaVencimento: bool,
 *             enviarAvisoVencimentoSmsAposVencimento: bool
 *         },
 *         pagamentoDivergente: array{
 *             tipoPagamentoDivergente: int,
 *             valorMinimoPagamentoDivergente: int
 *         },
 *         avalista: array{
 *             entidadeLegal: array{
 *                 identificadorReceitaFederal: string,
 *                 tipoPessoa: int,
 *                 nome: string
 *             }
 *         },
 *         indicadorRegistroNuclea: int,
 *         numeroParcela: int,
 *         tipoVencimento: array{
 *             tipoVencimento: int,
 *             quantidadeXDias: int,
 *             diaXDeCadaMes: int
 *         }
 *     }>
 * }
 */
final class GerarLoteCarne extends Endpoint
{
    /**
     * @param GerarLoteCarneRequest $lote
     */
    public function handle(string $convenio, array $lote): void
    {
        $this->post(new Request(
            path: "/ailos/cobranca/api/v2/boletos/gerar/carne/lote/convenios/{$convenio}",
            body: $lote
        ));
    }
}
