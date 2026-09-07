<?php

declare(strict_types=1);

namespace Ailos\Sdk\Cobranca\Context;

use Ailos\Sdk\Http\CurlHttp;
use Ailos\Sdk\Http\IHttp;
use Ailos\Sdk\Storage\IStorage;
use Ailos\Sdk\Storage\Storage;
use InvalidArgumentException;

final class CobrancaContext
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
        public readonly string $ambiente = 'homol',
        public readonly IStorage $storage = new Storage(),
        public readonly IHttp $http = new CurlHttp(),
        public readonly bool $catcherService = false,
        public readonly ?string $catcherUrl = null,
        public readonly ?string $catcherSecret = null
    ) {
        if (!array_key_exists($this->ambiente, self::URLS)) {
            throw new InvalidArgumentException(
                "Ambiente inválido '{$this->ambiente}'. Permitido: " . implode(', ', array_keys(self::URLS))
            );
        }
        $this->baseUrl = self::URLS[$this->ambiente];
    }
}
