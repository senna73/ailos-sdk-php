<?php

declare(strict_types=1);

namespace Ailos\Sdk\Entities;

use Ailos\Sdk\Framework\Entity;

class AvisoSms extends Entity
{
    public function __construct(
        public readonly int $enviarAvisoVencimentoSms,
        public readonly bool $enviarAvisoVencimentoSmsAntesVencimento,
        public readonly bool $enviarAvisoVencimentoSmsDiaVencimento,
        public readonly bool $enviarAvisoVencimentoSmsAposVencimento
    ) {
    }
}
