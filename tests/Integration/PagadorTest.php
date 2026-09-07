<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests\Integration;

use Ailos\Sdk\Endpoints\Pagador\AlterarPagador;
use Ailos\Sdk\Endpoints\Pagador\CadastrarPagador;
use Ailos\Sdk\Endpoints\Pagador\ListarPagadores;
use Ailos\Sdk\Tests\IntegrationTestCase;

/**
 * @phpstan-import-type AlterarPagadorRequest from AlterarPagador
 */
class PagadorTest extends IntegrationTestCase
{
    public function testListarPagadores(): void
    {
        $response = new ListarPagadores(parent::$context)->handle();

        self::assertArrayHasKey('pagadorResponse', $response);
    }

    public function testCadastrarPagador(): void
    {
        new CadastrarPagador(parent::$context)->handle($this->pagador());

        $this->addToAssertionCount(1);
    }

    public function testAlterarPagador(): void
    {
        new AlterarPagador(parent::$context)->handle($this->pagador());

        $this->addToAssertionCount(1);
    }

    /** @return AlterarPagadorRequest */
    private function pagador(): array
    {
        return [
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
                    'Pagamento referente ao contrato de prestação de serviços.',
                ],
                'dda' => true,
            ],
        ];
    }
}
