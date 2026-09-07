<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests\Integration;

use Ailos\Sdk\Endpoints\Instrucao\AnuenciaEletronica;
use Ailos\Sdk\Endpoints\Instrucao\AlterarFormaEmissao;
use Ailos\Sdk\Endpoints\Instrucao\CancelarNegativacao;
use Ailos\Sdk\Endpoints\Instrucao\CancelarProtesto;
use Ailos\Sdk\Endpoints\Instrucao\NegativarBoleto;
use Ailos\Sdk\Endpoints\Instrucao\ProtestoAutomatico;
use Ailos\Sdk\Endpoints\Instrucao\ProtestarBoleto;
use Ailos\Sdk\Tests\IntegrationTestCase;

/**
 * @phpstan-import-type CancelarNegativacaoRequest from CancelarNegativacao
 * @phpstan-import-type AlterarFormaEmissaoRequest from AlterarFormaEmissao
 * @phpstan-import-type ProtestoAutomaticoRequest from ProtestoAutomatico
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

    public function testAlterarFormaEmissao(): void
    {
        new AlterarFormaEmissao(parent::$context)->handle($this->alterarFormaEmissao());

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

    public function testProtestoAutomatico(): void
    {
        new ProtestoAutomatico(parent::$context)->handle($this->protestoAutomatico());

        $this->addToAssertionCount(1);
    }

    public function testProtestarBoleto(): void
    {
        new ProtestarBoleto(parent::$context)->handle($this->instrucoes());

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

    /** @return ProtestoAutomaticoRequest */
    private function protestoAutomatico(): array
    {
        return [
            'boletos' => [
                [
                    'numeroConvenio' => 101004,
                    'numeroBoleto' => 100001,
                    'diasProtesto' => 10,
                ],
            ],
        ];
    }

    /** @return AlterarFormaEmissaoRequest */
    private function alterarFormaEmissao(): array
    {
        return [
            'boletos' => [
                [
                    'numeroConvenio' => 101004,
                    'numeroBoleto' => 100001,
                    'emissao' => [
                        'forma' => 1,
                    ],
                ],
            ],
        ];
    }
}