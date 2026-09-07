<?php

declare(strict_types=1);

namespace Ailos\Sdk\Endpoints\Cobranca\Webhook;

use Ailos\Sdk\Endpoints\Endpoint;
use Ailos\Sdk\Http\Request;

/**
 * @phpstan-type CadastrarWebhookRequest array{
 *     evento: int,
 *     cooperadoId: string,
 *     url: string
 * }
 */
final class CadastrarWebhook extends Endpoint
{
    /**
     * @param CadastrarWebhookRequest $webhook
     */
    public function handle(array $webhook): void
    {
        $this->post(new Request(
            path: '/ailos/cobranca/api/v2/webhooks',
            body: $webhook
        ));
    }
}
