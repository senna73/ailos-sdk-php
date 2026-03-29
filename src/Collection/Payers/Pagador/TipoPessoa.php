<?php

declare(strict_types=1);

namespace Ailos\Sdk\Collection\Payers\Pagador;

enum TipoPessoa: int
{
    case FISICA  = 1;
    case JURIDICA = 2;
}
