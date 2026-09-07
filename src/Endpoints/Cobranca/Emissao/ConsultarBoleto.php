<?php

declare(strict_types=1);

namespace Ailos\Sdk\Endpoints\Cobranca\Emissao;

use Ailos\Sdk\Endpoints\Endpoint;
use Ailos\Sdk\Http\Request;

/**
 * @phpstan-type ConsultarBoletoResponse array{
 *     boleto: array{
 *         beneficiario: array{
 *             entidadeLegal: array{
 *                 identificadorReceitaFederal: string,
 *                 tipoPessoa: int,
 *                 nome: string
 *             },
 *             emails: list<array{
 *                 endereco: string
 *             }>,
 *             endereco: array{
 *                 cep: string,
 *                 logradouro: string,
 *                 numero: int,
 *                 complemento: string,
 *                 bairro: string,
 *                 cidade: array{
 *                     codigo: int|null,
 *                     codigoMunicipioIbge: int,
 *                     nome: string,
 *                     uf: string
 *                 }
 *             }
 *         },
 *         contaCorrente: array{
 *             codigo: int,
 *             numero: int,
 *             digito: int,
 *             cooperativa: array{
 *                 codigoBanco: string,
 *                 codigo: int,
 *                 nome: string
 *             }
 *         },
 *         convenioCobranca: array{
 *             numeroConvenioCobranca: int,
 *             codigoCarteiraCobranca: int
 *         },
 *         documento: array{
 *             numeroDocumento: int,
 *             descricaoDocumento: string,
 *             dataDocumento: string,
 *             especieDocumento: int,
 *             nossoNumero: string,
 *             identificadorUnicoTitulo: int
 *         },
 *         emissao: array{
 *             formaEmissao: int,
 *             dataEmissao: string
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
 *                 numero: int,
 *                 complemento: string,
 *                 bairro: string,
 *                 cidade: array{
 *                     codigo: int|null,
 *                     codigoMunicipioIbge: int,
 *                     nome: string,
 *                     uf: string
 *                 }
 *             },
 *             mensagemPagador: list<string>,
 *             dda: bool,
 *             ativo: bool
 *         },
 *         vencimento: array{
 *             dataVencimentoAtual: string,
 *             dataVencimentoOriginal: string
 *         },
 *         instrucao: array{
 *             valorAbatimento: int,
 *             tipoDesconto: int,
 *             descontos: list<array{
 *                 dataLimite: string,
 *                 valor: int
 *             }>,
 *             tipoMulta: int,
 *             valorMulta: int,
 *             tipoJurosMora: int,
 *             valorJurosMora: int
 *         },
 *         valorBoleto: array{
 *             valorOriginalTitulo: int,
 *             valorAtual: int,
 *             valorPago: int,
 *             valorJurosPago: int,
 *             valorMultaPago: int,
 *             valorOutrosDebitos: int,
 *             valorOutrosCreditos: int,
 *             valorJurosCalc: int,
 *             valorMultaCalc: int
 *         },
 *         avisoSms: array{
 *             enviarAvisoVencimentoSms: int,
 *             enviarAvisoVencimentoSmsAntesVencimento: bool,
 *             enviarAvisoVencimentoSmsDiaVencimento: bool,
 *             enviarAvisoVencimentoSmsAposVencimento: bool
 *         },
 *         pagamentoDivergente: array{
 *             tipoPagamentoDivergente: int,
 *             valorMinimoParaPagamentoDivergente: int
 *         },
 *         dataMovimentoDoSistema: string,
 *         avalista: array{
 *             entidadeLegalResponse: array{
 *                 identificadorReceitaFederal: string,
 *                 tipoPessoa: int,
 *                 nome: string
 *             }
 *         },
 *         codigoBarras: array{
 *             codigoBarras: string,
 *             linhaDigitavel: string
 *         },
 *         pagamento: array{
 *             indicadorPagamento: int,
 *             banco: mixed,
 *             agenciaPagamento: mixed,
 *             dataPagamento: string,
 *             dataBaixadoBoleto: string,
 *             dataCredito: string
 *         },
 *         listaInstrucao: list<string>,
 *         indicadorSituacaoBoleto: int,
 *         situacaoProcessoDda: int,
 *         serasa: array{
 *             flagNegativarSerasa: bool,
 *             diasNegativacaoSerasa: int,
 *             situacaoSerasa: int,
 *             dataOcorrenciaSerasa: string
 *         },
 *         protesto: array{
 *             tipoProstesto: int,
 *             diasProtesto: int,
 *             dataMovimentoProtesto: string,
 *             situacaoCartorio: int
 *         },
 *         pix: array{
 *             conciliacaoId: string,
 *             qrCode: string,
 *             copiaECola: string
 *         }
 *     }
 * }
 */
final class ConsultarBoleto extends Endpoint
{
    /**
     * @return ConsultarBoletoResponse
     */
    public function handle(string $convenio, string $numero): array
    {
        $response = $this->get(new Request(
            path: "/ailos/cobranca/api/v2/boletos/consultar/boleto/convenios/{$convenio}/{$numero}"
        ));

        /** @var ConsultarBoletoResponse $body */
        $body = $response->json();

        return $body;
    }
}
