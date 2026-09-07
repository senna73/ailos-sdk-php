<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests\Integration;

use Ailos\Sdk\Endpoints\Instrucao\AnuenciaEletronica;
use Ailos\Sdk\Endpoints\Instrucao\AlterarFormaEmissao;
use Ailos\Sdk\Endpoints\Instrucao\AlteracaoVencimento;
use Ailos\Sdk\Endpoints\Instrucao\Baixa;
use Ailos\Sdk\Endpoints\Instrucao\CancelarAbatimento;
use Ailos\Sdk\Endpoints\Instrucao\CancelarDescontos;
use Ailos\Sdk\Endpoints\Instrucao\CancelarEnvioSms;
use Ailos\Sdk\Endpoints\Instrucao\CancelarNegativacao;
use Ailos\Sdk\Endpoints\Instrucao\CancelarProtesto;
use Ailos\Sdk\Endpoints\Instrucao\ConcederAbatimento;
use Ailos\Sdk\Endpoints\Instrucao\ConcederDesconto;
use Ailos\Sdk\Endpoints\Instrucao\GerarEnvioSms;
use Ailos\Sdk\Endpoints\Instrucao\NegativarBoleto;
use Ailos\Sdk\Endpoints\Instrucao\ProtestoAutomatico;
use Ailos\Sdk\Endpoints\Instrucao\ProtestarBoleto;
use Ailos\Sdk\Tests\IntegrationTestCase;

/**
 * @phpstan-import-type CancelarNegativacaoRequest from CancelarNegativacao
 * @phpstan-import-type AlterarFormaEmissaoRequest from AlterarFormaEmissao
 * @phpstan-import-type AlteracaoVencimentoRequest from AlteracaoVencimento
 * @phpstan-import-type ProtestoAutomaticoRequest from ProtestoAutomatico
 * @phpstan-import-type GerarEnvioSmsRequest from GerarEnvioSms
 * @phpstan-import-type ConcederAbatimentoRequest from ConcederAbatimento
 * @phpstan-import-type ConcederDescontoRequest from ConcederDesconto
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

    public function testAlteracaoVencimento(): void
    {
        new AlteracaoVencimento(parent::$context)->handle($this->alteracaoVencimento());

        $this->addToAssertionCount(1);
    }

    public function testBaixa(): void
    {
        new Baixa(parent::$context)->handle($this->instrucoes());

        $this->addToAssertionCount(1);
    }

    public function testCancelarAbatimento(): void
    {
        new CancelarAbatimento(parent::$context)->handle($this->instrucoes());

        $this->addToAssertionCount(1);
    }

    public function testCancelarDescontos(): void
    {
        new CancelarDescontos(parent::$context)->handle($this->instrucoes());

        $this->addToAssertionCount(1);
    }

    public function testConcederAbatimento(): void
    {
        new ConcederAbatimento(parent::$context)->handle($this->concederAbatimento());

        $this->addToAssertionCount(1);
    }

    public function testConcederDesconto(): void
    {
        new ConcederDesconto(parent::$context)->handle($this->concederDesconto());

        $this->addToAssertionCount(1);
    }

    public function testCancelarEnvioSms(): void
    {
        new CancelarEnvioSms(parent::$context)->handle($this->instrucoes());

        $this->addToAssertionCount(1);
    }

    public function testGerarEnvioSms(): void
    {
        new GerarEnvioSms(parent::$context)->handle($this->gerarEnvioSms());

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

    /** @return GerarEnvioSmsRequest */
    private function gerarEnvioSms(): array
    {
        return [
            'boletos' => [
                [
                    'numeroConvenio' => 101004,
                    'numeroBoleto' => 100001,
                    'tipoEnvio' => 1,
                    'telefone' => [
                        'ddi' => '55',
                        'ddd' => '47',
                        'numero' => '999887766',
                    ],
                ],
            ],
        ];
    }

    /** @return ConcederAbatimentoRequest */
    private function concederAbatimento(): array
    {
        return [
            'boletos' => [
                [
                    'numeroConvenio' => 101004,
                    'numeroBoleto' => 100001,
                    'valorAbatimento' => 10,
                ],
            ],
        ];
    }

    /** @return ConcederDescontoRequest */
    private function concederDesconto(): array
    {
        return [
            'boletos' => [
                [
                    'numeroConvenio' => 101004,
                    'numeroBoleto' => 100001,
                    'tipoDesconto' => 1,
                    'descontos' => [
                        [
                            'valor' => 10,
                            'diasAteVencimento' => 5,
                        ],
                    ],
                ],
            ],
        ];
    }

    /** @return AlteracaoVencimentoRequest */
    private function alteracaoVencimento(): array
    {
        return [
            'boletos' => [
                [
                    'numeroConvenio' => 101004,
                    'numeroBoleto' => 100001,
                    'vencimento' => [
                        'dataVencimento' => '2026-09-15T16:16:48.886Z',
                    ],
                ],
            ],
        ];
    }
}