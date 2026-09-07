<?php

declare(strict_types=1);

namespace Ailos\Sdk;

use Ailos\Sdk\Cobranca\Auth\Auth;
use Ailos\Sdk\Cobranca\Auth\Jwt;
use Ailos\Sdk\Config\AilosContext;
use Ailos\Sdk\Cobranca\Endpoints\Emissao\ConsultarBoleto;
use Ailos\Sdk\Cobranca\Endpoints\Emissao\GerarBoleto;
use Ailos\Sdk\Cobranca\Endpoints\Emissao\GerarLoteBoleto;
use Ailos\Sdk\Cobranca\Endpoints\Emissao\GerarLoteCarne;
use Ailos\Sdk\Cobranca\Endpoints\Instrucao\AnuenciaEletronica;
use Ailos\Sdk\Cobranca\Endpoints\Instrucao\AlterarFormaEmissao;
use Ailos\Sdk\Cobranca\Endpoints\Instrucao\AlteracaoVencimento;
use Ailos\Sdk\Cobranca\Endpoints\Instrucao\Baixa;
use Ailos\Sdk\Cobranca\Endpoints\Instrucao\CancelarAbatimento;
use Ailos\Sdk\Cobranca\Endpoints\Instrucao\CancelarNegativacao;
use Ailos\Sdk\Cobranca\Endpoints\Instrucao\CancelarEnvioSms;
use Ailos\Sdk\Cobranca\Endpoints\Instrucao\CancelarDescontos;
use Ailos\Sdk\Cobranca\Endpoints\Instrucao\CancelarProtesto;
use Ailos\Sdk\Cobranca\Endpoints\Instrucao\ConcederAbatimento;
use Ailos\Sdk\Cobranca\Endpoints\Instrucao\ConcederDesconto;
use Ailos\Sdk\Cobranca\Endpoints\Instrucao\GerarEnvioSms;
use Ailos\Sdk\Cobranca\Endpoints\Instrucao\NegativarBoleto;
use Ailos\Sdk\Cobranca\Endpoints\Instrucao\ProtestoAutomatico;
use Ailos\Sdk\Cobranca\Endpoints\Instrucao\ProtestarBoleto;
use Ailos\Sdk\Cobranca\Endpoints\Pagador\AlterarPagador;
use Ailos\Sdk\Endpoints\Pagador\CadastrarPagador;
use Ailos\Sdk\Cobranca\Endpoints\Pagador\ConsultarPagador;
use Ailos\Sdk\Cobranca\Endpoints\Pagador\ListarPagadores;
use Ailos\Sdk\Cobranca\Endpoints\Webhook\CadastrarWebhook;
use Ailos\Sdk\Endpoints\Webhook\ConsultarWebhook;
use Ailos\Sdk\Cobranca\Endpoints\Webhook\ExcluirWebhook;
use Ailos\Sdk\Cobranca\Endpoints\Webhook\ListarWebhooks;

/**
 * @phpstan-import-type CadastrarPagadorRequest from CadastrarPagador
 * @phpstan-import-type AlterarPagadorRequest from AlterarPagador
 * @phpstan-import-type ConsultarPagadorResponse from ConsultarPagador
 * @phpstan-import-type ListarPagadoresResponse from ListarPagadores
 * @phpstan-import-type GerarBoletoRequest from GerarBoleto
 * @phpstan-import-type ConsultarBoletoResponse from ConsultarBoleto
 * @phpstan-import-type GerarLoteBoletoRequest from GerarLoteBoleto
 * @phpstan-import-type GerarLoteCarneRequest from GerarLoteCarne
 * @phpstan-import-type AnuenciaEletronicaRequest from AnuenciaEletronica
 * @phpstan-import-type AlterarFormaEmissaoRequest from AlterarFormaEmissao
 * @phpstan-import-type AlteracaoVencimentoRequest from AlteracaoVencimento
 * @phpstan-import-type BaixaRequest from Baixa
 * @phpstan-import-type CancelarAbatimentoRequest from CancelarAbatimento
 * @phpstan-import-type CancelarNegativacaoRequest from CancelarNegativacao
 * @phpstan-import-type CancelarEnvioSmsRequest from CancelarEnvioSms
 * @phpstan-import-type CancelarDescontosRequest from CancelarDescontos
 * @phpstan-import-type CancelarProtestoRequest from CancelarProtesto
 * @phpstan-import-type ConcederAbatimentoRequest from ConcederAbatimento
 * @phpstan-import-type ConcederDescontoRequest from ConcederDesconto
 * @phpstan-import-type GerarEnvioSmsRequest from GerarEnvioSms
 * @phpstan-import-type NegativarBoletoRequest from NegativarBoleto
 * @phpstan-import-type ProtestoAutomaticoRequest from ProtestoAutomatico
 * @phpstan-import-type ProtestarBoletoRequest from ProtestarBoleto
 * @phpstan-import-type CadastrarWebhookRequest from CadastrarWebhook
 */
final class Ailos
{
    public function __construct(private AilosContext $context)
    {
    }

    /**
     * @param array{state: string, code: string} $payload
     */
    public function handleJwtCallback(array $payload): void
    {
        $auth = new Auth($this->context);

        if ($payload['state'] !== $auth->getState()) {
            throw new \RuntimeException('State inválido no JWT.');
        }

        $jwt = Jwt::fromArray($payload);

        $auth->setJwt($jwt);
    }

    /**
        * @return ListarPagadoresResponse
     */
    public function listarPagadores(): array
    {
        return new ListarPagadores($this->context)->handle();
    }

    /**
     * @return ConsultarPagadorResponse
     */
    public function consultarPagador(string $documento): array
    {
        return new ConsultarPagador($this->context)->handle($documento);
    }

    /**
    * @param CadastrarPagadorRequest $pagador
     */
    public function cadastrarPagador(array $pagador): void
    {
        new CadastrarPagador($this->context)->handle($pagador);
    }

    /**
        * @param AlterarPagadorRequest $pagador
     */
    public function alterarPagador(array $pagador): void
    {
        new AlterarPagador($this->context)->handle($pagador);
    }

    /**
    * @return ConsultarBoletoResponse
     */
    public function consultarBoleto(string $convenio, string $numero): array
    {
        return new ConsultarBoleto($this->context)->handle($convenio, $numero);
    }

    /**
    * @param GerarBoletoRequest $boleto
     */
    public function gerarBoleto(string $convenio, array $boleto): void
    {
        new GerarBoleto($this->context)->handle($convenio, $boleto);
    }

    /**
    * @param GerarLoteBoletoRequest $lote
     */
    public function gerarLoteBoleto(string $convenio, array $lote): void
    {
        new GerarLoteBoleto($this->context)->handle($convenio, $lote);
    }

    /**
    * @param GerarLoteCarneRequest $lote
     */
    public function gerarLoteCarne(string $convenio, array $lote): void
    {
        new GerarLoteCarne($this->context)->handle($convenio, $lote);
    }

    /**
     * @param CancelarNegativacaoRequest $instrucoes
     */
    public function cancelarNegativacao(array $instrucoes): void
    {
        new CancelarNegativacao($this->context)->handle($instrucoes);
    }

    /**
     * @param AnuenciaEletronicaRequest $instrucoes
     */
    public function anuenciaEletronica(array $instrucoes): void
    {
        new AnuenciaEletronica($this->context)->handle($instrucoes);
    }

    /**
     * @param AlterarFormaEmissaoRequest $instrucoes
     */
    public function alterarFormaEmissao(array $instrucoes): void
    {
        new AlterarFormaEmissao($this->context)->handle($instrucoes);
    }

    /**
     * @param AlteracaoVencimentoRequest $instrucoes
     */
    public function alteracaoVencimento(array $instrucoes): void
    {
        new AlteracaoVencimento($this->context)->handle($instrucoes);
    }

    /**
     * @param BaixaRequest $instrucoes
     */
    public function baixa(array $instrucoes): void
    {
        new Baixa($this->context)->handle($instrucoes);
    }

    /**
     * @param CancelarAbatimentoRequest $instrucoes
     */
    public function cancelarAbatimento(array $instrucoes): void
    {
        new CancelarAbatimento($this->context)->handle($instrucoes);
    }

    /**
     * @param CancelarDescontosRequest $instrucoes
     */
    public function cancelarDescontos(array $instrucoes): void
    {
        new CancelarDescontos($this->context)->handle($instrucoes);
    }

    /**
     * @param ConcederAbatimentoRequest $instrucoes
     */
    public function concederAbatimento(array $instrucoes): void
    {
        new ConcederAbatimento($this->context)->handle($instrucoes);
    }

    /**
     * @param ConcederDescontoRequest $instrucoes
     */
    public function concederDesconto(array $instrucoes): void
    {
        new ConcederDesconto($this->context)->handle($instrucoes);
    }

    /**
     * @param CancelarEnvioSmsRequest $instrucoes
     */
    public function cancelarEnvioSms(array $instrucoes): void
    {
        new CancelarEnvioSms($this->context)->handle($instrucoes);
    }

    /**
     * @param GerarEnvioSmsRequest $instrucoes
     */
    public function gerarEnvioSms(array $instrucoes): void
    {
        new GerarEnvioSms($this->context)->handle($instrucoes);
    }

    /**
     * @param NegativarBoletoRequest $instrucoes
     */
    public function negativarBoleto(array $instrucoes): void
    {
        new NegativarBoleto($this->context)->handle($instrucoes);
    }

    /**
     * @param CancelarProtestoRequest $instrucoes
     */
    public function cancelarProtesto(array $instrucoes): void
    {
        new CancelarProtesto($this->context)->handle($instrucoes);
    }

    /**
     * @param ProtestoAutomaticoRequest $instrucoes
     */
    public function protestoAutomatico(array $instrucoes): void
    {
        new ProtestoAutomatico($this->context)->handle($instrucoes);
    }

    /**
     * @param ProtestarBoletoRequest $instrucoes
     */
    public function protestarBoleto(array $instrucoes): void
    {
        new ProtestarBoleto($this->context)->handle($instrucoes);
    }

    /**
     * @param CadastrarWebhookRequest $webhook
     */
    public function cadastrarWebhook(array $webhook): void
    {
        new CadastrarWebhook($this->context)->handle($webhook);
    }

    /**
     * @return array<string, mixed>
     */
    public function listarWebhooks(int $evento): array
    {
        return new ListarWebhooks($this->context)->handle($evento);
    }

    /**
     * @return array<string, mixed>
     */
    public function consultarWebhook(string $identificador): array
    {
        return new ConsultarWebhook($this->context)->handle($identificador);
    }

    public function excluirWebhook(int $evento): void
    {
        new ExcluirWebhook($this->context)->handle($evento);
    }

}
