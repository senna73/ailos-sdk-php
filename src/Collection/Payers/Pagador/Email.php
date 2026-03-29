<?php

declare(strict_types=1);

namespace Ailos\Sdk\Collection\Payers\Pagador;

final readonly class Email
{
    public function __construct(private string $endereco)
    {
        if (!filter_var($this->endereco, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("E-mail inválido: {$this->endereco}");
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return ['endereco' => $this->endereco];
    }
}
