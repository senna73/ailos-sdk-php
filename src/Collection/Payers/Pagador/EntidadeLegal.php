<?php

declare(strict_types=1);

namespace Ailos\Sdk\Collection\Payers\Pagador;

final readonly class EntidadeLegal
{
    public function __construct(
        private string $identificadorReceitaFederal,
        private TipoPessoa $tipoPessoa,
        private string $nome
    ) {
        if (empty($this->identificadorReceitaFederal)) {
            throw new \InvalidArgumentException('O identificador da Receita Federal não pode ser vazio.');
        }

        if (empty($this->nome)) {
            throw new \InvalidArgumentException('O nome não pode ser vazio.');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
          'identificador_receita_federal' => $this->identificadorReceitaFederal,
          'tipo_pessoa' => $this->tipoPessoa,
          'nome_pessoa' => $this->nome,
        ];
    }
}
