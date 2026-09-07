<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests\Integration\Cobranca;

use Ailos\Sdk\Cobranca\Endpoints\Emissao\ConsultarBoleto;
use Ailos\Sdk\Cobranca\Endpoints\Emissao\GerarBoleto;
use Ailos\Sdk\Cobranca\Endpoints\Emissao\GerarLoteBoleto;
use Ailos\Sdk\Cobranca\Endpoints\Emissao\GerarLoteCarne;
use Ailos\Sdk\Tests\CobrancaTestCase;

/**
 * @phpstan-import-type GerarBoletoRequest from GerarBoleto
 */
class BoletoTest extends CobrancaTestCase
{
    private const string CONVENIO = '101004';

    public function testConsultarBoleto(): void
    {
        $response = new ConsultarBoleto(parent::$context)->handle(self::CONVENIO, '100001');

        self::assertArrayHasKey('boleto', $response);
    }

    public function testGerarBoleto(): void
    {
        new GerarBoleto(parent::$context)->handle(self::CONVENIO, $this->boleto());

        $this->addToAssertionCount(1);
    }

    public function testGerarLoteBoletos(): void
    {
        $lote = [
            'convenioCobranca' => [
                'codigoCarteiraCobranca' => 1,
            ],
            'boletos' => [
                $this->boleto(),
            ],
        ];

        new GerarLoteBoleto(parent::$context)->handle(self::CONVENIO, $lote);

        $this->addToAssertionCount(1);
    }

    public function testGerarLoteCarne(): void
    {
        $lote = [
            'convenioCobranca' => [
                'codigoCarteiraCobranca' => 1,
            ],
            'carnes' => [
                [
                    ...$this->boleto(),
                    'numeroParcela' => 1,
                    'tipoVencimento' => [
                        'tipoVencimento' => 1,
                        'quantidadeXDias' => 0,
                        'diaXDeCadaMes' => 0,
                    ],
                ],
            ],
        ];

        new GerarLoteCarne(parent::$context)->handle(self::CONVENIO, $lote);

        $this->addToAssertionCount(1);
    }

    /** @return GerarBoletoRequest */
    private function boleto(): array
    {
        return  [
            'convenioCobranca' => [
                'codigoCarteiraCobranca' => 1,
            ],
            'documento' => [
                'numeroDocumento' => rand(100000, 999999),
                'descricaoDocumento' => 'Mensalidade',
                'especieDocumento' => 1,
            ],
            'emissao' => [
                'formaEmissao' => 2,
                'dataEmissaoDocumento' => '2026-09-01T10:00:00.000Z',
            ],
            'pagador' => [
                'entidadeLegal' => [
                    'identificadorReceitaFederal' => '00384870000101',
                    'tipoPessoa' => 2,
                    'nome' => 'Empresa Exemplo LTDA',
                ],
                'telefone' => [
                    'ddi' => '55',
                    'ddd' => '47',
                    'numero' => '999887766',
                ],
                'emails' => [
                    [
                        'endereco' => 'financeiro@empresaexemplo.com.br',
                    ],
                ],
                'endereco' => [
                    'cep' => '89010000',
                    'logradouro' => 'Rua XV de Novembro',
                    'numero' => '1000',
                    'complemento' => 'Sala 201',
                    'bairro' => 'Centro',
                    'cidade' => 'Blumenau',
                    'uf' => 'SC',
                ],
                'mensagemPagador' => [
                    'Pagamento referente ao contrato.',
                ],
            ],
            'vencimento' => [
                'dataVencimento' => '2026-09-15T23:59:59.000Z',
            ],
            'instrucoes' => [
                'valorAbatimento' => 0,
                'tipoDesconto' => 1,
                'descontos' => [
                    [
                        'valor' => 10,
                        'diasAteVencimento' => 5,
                    ],
                ],
                'tipoMulta' => 1,
                'valorMulta' => 2,
                'tipoJurosMora' => 1,
                'valorJurosMora' => 1,
                'diasNegativacao' => 10,
                'diasProtesto' => 0,
            ],
            'valorBoleto' => [
                'valorNominal' => 1500,
            ],
            'pagamentoDivergente' => [
                'tipoPagamentoDivergente' => 0,
                'valorMinimoPagamentoDivergente' => 0,
            ],
            'avisoSms' => [
                'enviarAvisoVencimentoSms' => 1,
                'enviarAvisoVencimentoSmsAntesVencimento' => true,
                'enviarAvisoVencimentoSmsDiaVencimento' => true,
                'enviarAvisoVencimentoSmsAposVencimento' => false,
            ],
            'avalista' => [
                'entidadeLegal' => [
                    'identificadorReceitaFederal' => '00000000000191',
                    'tipoPessoa' => 2,
                    'nome' => 'Garantia Comercial LTDA',
                ],
            ],
            'indicadorRegistroNuclea' => 1,
            'bolePix' => true,
        ];
    }
}
