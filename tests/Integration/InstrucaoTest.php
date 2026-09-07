<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests\Integration;

use Ailos\Sdk\Endpoints\Instrucao\AnuenciaEletronica;
use Ailos\Sdk\Endpoints\Instrucao\CancelarNegativacao;
use Ailos\Sdk\Endpoints\Instrucao\CancelarProtesto;
use Ailos\Sdk\Endpoints\Instrucao\NegativarBoleto;
use Ailos\Sdk\Tests\IntegrationTestCase;

/**
 * @phpstan-import-type CancelarNegativacaoRequest from CancelarNegativacao
 */
class InstrucaoTest extends IntegrationTestCase
{
    public function testCancelarNegativacao(): void
    {
        new CancelarNegativacao(parent::$context)->handle($this->instrucoes());

        $this->addToAssertionCount(1);
    }

    public function testAnuenciaEletronica(): void
    {
        new AnuenciaEletronica(parent::$context)->handle($this->instrucoes());

        $this->addToAssertionCount(1);
    }

    public function testNegativarBoleto(): void
    {
        new NegativarBoleto(parent::$context)->handle($this->instrucoes());

        $this->addToAssertionCount(1);
    }

    public function testCancelarProtesto(): void
    {
        new CancelarProtesto(parent::$context)->handle($this->instrucoes());

        $this->addToAssertionCount(1);
    }

    /** @return CancelarNegativacaoRequest */
    private function instrucoes(): array
    {
        return [
            'boletos' => [
                [
                    'numeroConvenio' => 101004,
                    'numeroBoleto' => 100001,
                ],
            ],
        ];
    }
}