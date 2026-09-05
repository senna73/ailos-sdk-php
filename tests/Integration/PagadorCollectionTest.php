<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests\Integration;

use Ailos\Sdk\Collections\PagadorCollection;
use Ailos\Sdk\Entities\Pagador;
use Ailos\Sdk\Tests\IntegrationTestCase;

class PagadorCollectionTest extends IntegrationTestCase
{
    public function testCadastrarPagador(): void
    {
        $this->collection()->cadastrarPagador($this->pagador());

        $this->addToAssertionCount(1);
    }

    public function testAlterarPagador(): void
    {
        $this->collection()->alterarPagador($this->pagador());

        $this->addToAssertionCount(1);
    }

    private function collection(): PagadorCollection
    {
        return new PagadorCollection(self::$enviroment, self::$config);
    }

    private function pagador(): Pagador
    {
        return Pagador::fromArray([
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
            'emails' => [
                ['endereco' => 'pagador@example.com'],
            ],
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
        ]);
    }
}
