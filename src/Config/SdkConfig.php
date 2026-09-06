<?php

declare(strict_types=1);

namespace Ailos\Sdk\Config;

use Ailos\Sdk\Auth\Storage\ITokenStorage;
use Ailos\Sdk\Auth\Storage\TokenStorage;
use Ailos\Sdk\Http\CurlHttp;
use Ailos\Sdk\Http\IHttp;

final class SdkConfig
{
    public function __construct(
        public readonly ITokenStorage $storage = new TokenStorage(),
        public readonly IHttp $http = new CurlHttp(),
        public readonly bool $catcherService = false,
        public readonly ?string $catcherUrl = null,
        public readonly ?string $catcherSecret = null
    ) {
    }
}
