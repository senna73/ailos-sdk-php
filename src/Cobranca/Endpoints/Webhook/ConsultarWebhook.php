<?php

declare(strict_types=1);

namespace Ailos\Sdk\Cobranca\Endpoints\Webhook;

use Ailos\Sdk\Http\Endpoint;
use Ailos\Sdk\Http\Request;

final class ConsultarWebhook extends Endpoint
{
    /**
     * @return array<string, mixed>
     */
    public function handle(string $identificador): array
    {
        $response = $this->get(new Request(
            path: "/ailos/cobranca/api/v2/webhooks/{$identificador}"
        ));

        return $response->json();
    }
}