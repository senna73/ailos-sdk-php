<?php

declare(strict_types=1);

namespace Ailos\Sdk\Cobranca\Endpoints\Webhook;

use Ailos\Sdk\Http\Endpoint;
use Ailos\Sdk\Http\Request;

final class ListarWebhooks extends Endpoint
{
    /**
     * @return array<string, mixed>
     */
    public function handle(int $evento): array
    {
        $response = $this->get(new Request(
            path: '/ailos/cobranca/api/v2/webhooks/',
            query: ['evento' => $evento]
        ));

        return $response->json();
    }
}
