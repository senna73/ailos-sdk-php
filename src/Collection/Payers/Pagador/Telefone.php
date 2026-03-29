<?php

declare(strict_types=1);

namespace Ailos\Sdk\Collection\Payers\Pagador;

final readonly class Telefone
{
    public function __construct(
        private string $ddi,
        private string $ddd,
        private string $numero,
    ) {
        if (empty($this->ddd) || empty($this->numero)) {
            throw new \InvalidArgumentException('DDD e número do telefone são obrigatórios.');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'ddi'    => $this->ddi,
            'ddd'    => $this->ddd,
            'numero' => $this->numero,
        ];
    }
}
