<?php

declare(strict_types=1);

namespace Ailos\Sdk\Config;

final class AilosContext
{
    public function __construct(
        public readonly Environment $environment,
        public readonly SdkConfig $config,
    ) {
    }
}
