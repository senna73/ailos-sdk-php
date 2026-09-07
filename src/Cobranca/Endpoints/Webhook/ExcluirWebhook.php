<?php

declare(strict_types=1);

namespace Ailos\Sdk\Cobranca\Endpoints\Webhook;

use Ailos\Sdk\Http\Endpoint;
use Ailos\Sdk\Http\Request;

final class ExcluirWebhook extends Endpoint
{
    public function handle(int $evento): void
    {
        $this->delete(new Request(
            path: "/ailos/cobranca/api/v2/webhooks/{$evento}"
        ));
    }
}
