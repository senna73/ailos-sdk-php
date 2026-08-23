<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests\Integration;

use Ailos\Sdk\Collections\BoletoCollection;
use Ailos\Sdk\Entities\Boleto;
use Ailos\Sdk\Tests\IntegrationTestCase;

class BoletoCollectionTest extends IntegrationTestCase
{
    public function testGerarUnicoBoleto(): void
    {
        $collection = new BoletoCollection(self::$enviroment);

        $boleto = Boleto::fromArray([
            'convenioCobranca' => [
                'codigoCarteiraCobranca' => 101004,
            ],
            'documento' => [
                'numeroDocumento' => 123456789,
                'descricaoDocumento' => 'Fatura de servicos',
                'especieDocumento' => 1,
            ],
            'emissao' => [
                'formaEmissao' => 1,
                'dataEmissaoDocumento' => '2026-08-22',
            ],
            'pagador' => [
                'entidadeLegal' => [
                    'identificadorReceitaFederal' => '12345678000190',
                    'tipoPessoa' => 2,
                    'nome' => 'Empresa Mock Ltda',
                ],
                'telefone' => [
                    'ddi' => '55',
                    'ddd' => '47',
                    'numero' => '999999999',
                ],
                'emails' => [['endereco' => 'financeiro@example.com']],
                'endereco' => [
                    'cep' => '89010000',
                    'logradouro' => 'Rua das Flores',
                    'numero' => '100',
                    'complemento' => 'Sala 2',
                    'bairro' => 'Centro',
                    'cidade' => 'Blumenau',
                    'uf' => 'SC',
                ],
                'mensagemPagador' => ['Pagamento referente a servicos prestados'],
                'dda' => true,
            ],
            'vencimento' => [
                'dataVencimento' => '2026-09-22',
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
                'valorNominal' => 150000,
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
                    'identificadorReceitaFederal' => '98765432000110',
                    'tipoPessoa' => 2,
                    'nome' => 'Avalista Mock Ltda',
                ],
            ],
            'indicadorRegistroNuclea' => 1,
        ]);

        $collection->gerarUnicoBoleto('101004', $boleto);

        $this->addToAssertionCount(1);
    }
}
