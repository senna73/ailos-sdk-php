<?php

declare(strict_types=1);

namespace Ailos\Sdk\Entities;

use Ailos\Sdk\Framework\Entity;
use Ailos\Sdk\Framework\HttpClient\CurlHttpClient;
use Ailos\Sdk\Framework\HttpClient\IHttpClient;
use Ailos\Sdk\Framework\Storage\ApcuStorage;
use Ailos\Sdk\Framework\Storage\IStorage;

class SdkConfig extends Entity
{
    public function __construct(
        public readonly IStorage $storage = new ApcuStorage(),
        public readonly IHttpClient $http = new CurlHttpClient()
    ) {
    }
}
