<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests\Integration\Auth;

use Ailos\Sdk\Collection\Payers\Pagador\Email;
use Ailos\Sdk\Collection\Payers\Pagador\Endereco;
use Ailos\Sdk\Collection\Payers\Pagador\EntidadeLegal;
use Ailos\Sdk\Collection\Payers\Pagador\Pagador;
use Ailos\Sdk\Collection\Payers\Pagador\Telefone;
use Ailos\Sdk\Collection\Payers\Pagador\TipoPessoa;
use Ailos\Sdk\Tests\Integration\IntegrationTestCase;

class AuthenticationTest extends IntegrationTestCase
{
    public function test_auth_from_real_api(): void
    {
        //        $this->sdk->auth()->authenticate();

        $pagador = new Pagador(
            entidadeLegal: new EntidadeLegal(
                identificadorReceitaFederal: '123.456.789-00',
                tipoPessoa: TipoPessoa::FISICA,
                nome: 'João Silva',
            ),
            telefone: new Telefone(ddi: '55', ddd: '11', numero: '999999999'),
            emails: [
                new Email('joao@email.com'),
                new Email('joao2@email.com'),
            ],
            endereco: new Endereco(
                cep: '01310-100',
                logradouro: 'Av. Paulista',
                numero: '1000',
                bairro: 'Bela Vista',
                cidade: 'São Paulo',
                uf: 'SP',
                complemento: 'Apto 42',
            ),
            mensagensPagador: ['Mensagem 1', 'Mensagem 2'],
            dda: true,
        );

        $this->sdk->pagador()->registrarPagador($pagador);
        self::assertTrue(true);
    }
}
