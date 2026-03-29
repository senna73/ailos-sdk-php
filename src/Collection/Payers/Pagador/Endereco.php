<?php

declare(strict_types=1);

namespace Ailos\Sdk\Collection\Payers\Pagador;

final readonly class Endereco
{
    public function __construct(
        public string  $cep,
        public string  $logradouro,
        public string  $numero,
        public string  $bairro,
        public string  $cidade,
        public string  $uf,
        public ?string $complemento = null,
    ) {
        if (strlen($this->uf) !== 2) {
            throw new \InvalidArgumentException('UF deve ter exatamente 2 caracteres.');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'cep'         => $this->cep,
            'logradouro'  => $this->logradouro,
            'numero'      => $this->numero,
            'complemento' => $this->complemento,
            'bairro'      => $this->bairro,
            'cidade'      => $this->cidade,
            'uf'          => $this->uf,
        ], fn ($v) => $v !== null);
    }
}
