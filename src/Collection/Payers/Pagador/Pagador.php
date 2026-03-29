<?php

declare(strict_types=1);

namespace Ailos\Sdk\Collection\Payers\Pagador;

final readonly class Pagador
{
    /**
     * @param EntidadeLegal $entidadeLegal
     * @param Telefone|null $telefone
     * @param array<Email> $emails
     * @param Endereco|null $endereco
     * @param array<string> $mensagensPagador
     * @param bool $dda
     */
    public function __construct(
        public EntidadeLegal $entidadeLegal,
        public ?Telefone     $telefone         = null,
        public array         $emails           = [],
        public ?Endereco     $endereco         = null,
        public array         $mensagensPagador = [],
        public bool          $dda              = false,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'pagador' => array_filter([
                'entidadeLegal'    => $this->entidadeLegal->toArray(),
                'telefone'         => $this->telefone?->toArray(),
                'emails'           => array_map(fn (Email $e) => $e->toArray(), $this->emails) ?: null,
                'endereco'         => $this->endereco?->toArray(),
                'mensagemPagador'  => $this->mensagensPagador ?: null,
                'dda'              => $this->dda,
            ], fn ($v) => $v !== null),
        ];
    }
}
