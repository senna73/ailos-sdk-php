<?php

declare(strict_types=1);

namespace Ailos\Sdk;

use Ailos\Sdk\Auth\Auth;
use Ailos\Sdk\Auth\Jwt;
use Ailos\Sdk\Config\AilosContext;
use Ailos\Sdk\Endpoints\Boleto\ConsultarBoleto;
use Ailos\Sdk\Endpoints\Boleto\GerarBoleto;
use Ailos\Sdk\Endpoints\Boleto\GerarLoteBoleto;
use Ailos\Sdk\Endpoints\Boleto\GerarLoteCarne;
use Ailos\Sdk\Endpoints\Pagador\AlterarPagador;
use Ailos\Sdk\Endpoints\Pagador\CadastrarPagador;
use Ailos\Sdk\Endpoints\Pagador\ListarPagadores;

/**
 * @phpstan-import-type CadastrarPagadorRequest from CadastrarPagador
 * @phpstan-import-type AlterarPagadorRequest from AlterarPagador
 * @phpstan-import-type ListarPagadoresResponse from ListarPagadores
 * @phpstan-import-type GerarBoletoRequest from GerarBoleto
 * @phpstan-import-type ConsultarBoletoResponse from ConsultarBoleto
 * @phpstan-import-type GerarLoteBoletoRequest from GerarLoteBoleto
 * @phpstan-import-type GerarLoteCarneRequest from GerarLoteCarne
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

        if (!isset($payload['state'])) {
            throw new \RuntimeException('State não encontrado no JWT.');
        }

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
        return new AlterarPagador($this->context)->handle($pagador);
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
        return new GerarBoleto($this->context)->handle($convenio, $boleto);
    }

    /**
    * @param GerarLoteBoletoRequest $lote
     */
    public function gerarLoteBoleto(string $convenio, array $lote): void
    {
        return new GerarLoteBoleto($this->context)->handle($convenio, $lote);
    }

    /**
    * @param GerarLoteCarneRequest $lote
     */
    public function gerarLoteCarne(string $convenio, array $lote): void
    {
        return new GerarLoteCarne($this->context)->handle($convenio, $lote);
    }

}
