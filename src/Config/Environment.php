<?php

declare(strict_types=1);

namespace Ailos\Sdk\Config;

use InvalidArgumentException;

final class Environment
{
    private const array URLS = [
        'homol' => 'https://apiendpointhml.ailos.coop.br',
        'prod'  => 'https://apiendpoint.ailos.coop.br',
    ];

    public string $baseUrl;

    public function __construct(
        public readonly string $consumerKey,
        public readonly string $consumerSecret,
        public readonly string $urlCallback,
        public readonly string $developerKey,
        public readonly string $codigoCooperativa,
        public readonly string $codigoConta,
        public readonly string $senha,
        public readonly string $ambiente = 'homol'
    ) {
        if (!array_key_exists($this->ambiente, self::URLS)) {
            throw new InvalidArgumentException(
                "Ambiente inválido '{$this->ambiente}'. Permitido: " . implode(', ', array_keys(self::URLS))
            );
        }
        $this->baseUrl = self::URLS[$this->ambiente];
    }
}
