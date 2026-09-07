<?php

declare(strict_types=1);

namespace Ailos\Sdk;

use Ailos\Sdk\Auth\Auth;
use Ailos\Sdk\Auth\Jwt;
use Ailos\Sdk\Config\AilosContext;
use Ailos\Sdk\Endpoints\Emissao\ConsultarBoleto;
use Ailos\Sdk\Endpoints\Emissao\GerarBoleto;
use Ailos\Sdk\Endpoints\Emissao\GerarLoteBoleto;
use Ailos\Sdk\Endpoints\Emissao\GerarLoteCarne;
use Ailos\Sdk\Endpoints\Instrucao\AnuenciaEletronica;
use Ailos\Sdk\Endpoints\Instrucao\CancelarNegativacao;
use Ailos\Sdk\Endpoints\Instrucao\CancelarProtesto;
use Ailos\Sdk\Endpoints\Instrucao\NegativarBoleto;
use Ailos\Sdk\Endpoints\Instrucao\ProtestoAutomatico;
use Ailos\Sdk\Endpoints\Pagador\AlterarPagador;
use Ailos\Sdk\Endpoints\Pagador\CadastrarPagador;
use Ailos\Sdk\Endpoints\Pagador\ConsultarPagador;
use Ailos\Sdk\Endpoints\Pagador\ListarPagadores;

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
 * @phpstan-import-type CancelarNegativacaoRequest from CancelarNegativacao
 * @phpstan-import-type CancelarProtestoRequest from CancelarProtesto
 * @phpstan-import-type NegativarBoletoRequest from NegativarBoleto
 * @phpstan-import-type ProtestoAutomaticoRequest from ProtestoAutomatico
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

}
