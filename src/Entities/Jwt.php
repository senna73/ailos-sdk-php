<?php

declare(strict_types=1);

namespace Ailos\Sdk\Entities;

use Ailos\Sdk\Framework\Entity;

class Jwt extends Entity
{
    public function __construct(
        public readonly string $state,
        public readonly string $code,
    ) {
    }
}
