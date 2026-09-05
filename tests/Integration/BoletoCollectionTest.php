<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests\Integration;

use Ailos\Sdk\Collections\BoletoCollection;
use Ailos\Sdk\Entities\Boleto;
use Ailos\Sdk\Entities\BoletoCarne;
use Ailos\Sdk\Entities\BoletoCarneLote;
use Ailos\Sdk\Entities\BoletoLote;
use Ailos\Sdk\Tests\IntegrationTestCase;

class BoletoCollectionTest extends IntegrationTestCase
{
    private const string CONVENIO = '101004';

    private const string NUMERO_BOLETO = '123456789';

    public function testConsultarBoleto(): void
    {
        $boleto = $this->collection()->consultarBoleto(
            self::CONVENIO,
            self::NUMERO_BOLETO,
        );

        $this->assertInstanceOf(Boleto::class, $boleto);
    }

    public function testGerarUnicoBoleto(): void
    {
        $this->collection()->gerarUnicoBoleto(
            self::CONVENIO,
            $this->boleto(),
        );

        $this->addToAssertionCount(1);
    }

    public function testGerarLoteBoletos(): void
    {
        $boleto = $this->boleto();
        $lote = BoletoLote::fromArray([
            'convenioCobranca' => [
                'codigoCarteiraCobranca' => 1,
            ],
            'boletos' => [$boleto],
        ]);

        $this->collection()->gerarLoteBoletos(
            self::CONVENIO,
            $lote,
        );

        $this->addToAssertionCount(1);
    }

    public function testGerarLoteCarne(): void
    {
        $lote = BoletoCarneLote::fromArray([
            'convenioCobranca' => [
                'codigoCarteiraCobranca' => 1,
            ],
            'carnes' => [$this->carne()],
        ]);

        $this->collection()->gerarLoteCarne(
            self::CONVENIO,
            $lote,
        );

        $this->addToAssertionCount(1);
    }

    private function collection(): BoletoCollection
    {
        return new BoletoCollection(self::$enviroment, self::$config);
    }

    private function boleto(): Boleto
    {
        return Boleto::fromArray($this->boletoData());
    }

    private function carne(): BoletoCarne
    {
        return BoletoCarne::fromArray([
            ...$this->boletoData(),
            'numeroParcela' => 1,
            'tipoVencimento' => [
                'tipoVencimento' => 1,
                'quantidadeXDias' => 0,
                'diaXDeCadaMes' => 0,
            ],
        ]);
    }

    /** @return array<string, mixed> */
    private function boletoData(): array
    {
        return [
            'convenioCobranca' => [
                'codigoCarteiraCobranca' => 1,
            ],
            'documento' => [
                'numeroDocumento' => 123456789,
                'descricaoDocumento' => 'Teste de integracao',
                'especieDocumento' => 1,
            ],
            'emissao' => [
                'formaEmissao' => 1,
                'dataEmissaoDocumento' => '2026-09-04',
            ],
            'pagador' => [
                'entidadeLegal' => [
                    'identificadorReceitaFederal' => '12345678901',
                    'tipoPessoa' => 1,
                    'nome' => 'Pagador Teste',
                ],
                'telefone' => [
                    'ddi' => '55',
                    'ddd' => '47',
                    'numero' => '999999999',
                ],
                'emails' => [],
                'endereco' => [
                    'cep' => '89000000',
                    'logradouro' => 'Rua de Teste',
                    'numero' => '100',
                    'complemento' => '',
                    'bairro' => 'Centro',
                    'cidade' => 'Blumenau',
                    'uf' => 'SC',
                ],
                'mensagemPagador' => [],
                'dda' => false,
            ],
            'vencimento' => [
                'dataVencimento' => '2026-12-31',
            ],
            'instrucoes' => [
                'valorAbatimento' => 0.0,
                'tipoDesconto' => 0,
                'descontos' => [],
                'tipoMulta' => 0,
                'valorMulta' => 0.0,
                'tipoJurosMora' => 0,
                'valorJurosMora' => 0.0,
                'diasNegativacao' => 0,
                'diasProtesto' => 0,
            ],
            'valorBoleto' => [
                'valorNominal' => 10000,
            ],
            'avisoSms' => [
                'enviarAvisoVencimentoSms' => 0,
                'enviarAvisoVencimentoSmsAntesVencimento' => false,
                'enviarAvisoVencimentoSmsDiaVencimento' => false,
                'enviarAvisoVencimentoSmsAposVencimento' => false,
            ],
            'pagamentoDivergente' => [
                'tipoPagamentoDivergente' => 0,
                'valorMinimoPagamentoDivergente' => 0,
            ],
            'avalista' => [
                'entidadeLegal' => [
                    'identificadorReceitaFederal' => '98765432100',
                    'tipoPessoa' => 1,
                    'nome' => 'Avalista Teste',
                ],
            ],
            'indicadorRegistroNuclea' => 0,
        ];
    }
}
