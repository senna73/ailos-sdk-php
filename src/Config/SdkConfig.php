<?php

declare(strict_types=1);

namespace Ailos\Sdk\Config;

use Ailos\Sdk\Storage\IStorage;
use Ailos\Sdk\Storage\TokenStorage;
use Ailos\Sdk\Http\CurlHttp;
use Ailos\Sdk\Http\IHttp;

final class SdkConfig
{
    public function __construct(
        public readonly IStorage $storage = new TokenStorage(),
        public readonly IHttp $http = new CurlHttp(),
        public readonly bool $catcherService = false,
        public readonly ?string $catcherUrl = null,
        public readonly ?string $catcherSecret = null
    ) {
    }
}
