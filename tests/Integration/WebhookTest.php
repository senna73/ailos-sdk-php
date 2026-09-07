<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests\Integration;

use Ailos\Sdk\Endpoints\Cobranca\Webhook\CadastrarWebhook;
use Ailos\Sdk\Endpoints\Webhook\ConsultarWebhook;
use Ailos\Sdk\Endpoints\Cobranca\Webhook\ExcluirWebhook;
use Ailos\Sdk\Endpoints\Cobranca\Webhook\ListarWebhooks;
use Ailos\Sdk\Tests\IntegrationTestCase;

/**
 * @phpstan-import-type CadastrarWebhookRequest from CadastrarWebhook
 */
class WebhookTest extends IntegrationTestCase
{
    public function testCadastrarWebhook(): void
    {
        new CadastrarWebhook(parent::$context)->handle($this->webhook());

        $this->addToAssertionCount(1);
    }

    public function testListarWebhooks(): void
    {
        $response = new ListarWebhooks(parent::$context)->handle(1);

        self::assertIsArray($response);
    }

    public function testConsultarWebhook(): void
    {
        $response = new ConsultarWebhook(parent::$context)->handle('1');

        self::assertIsArray($response);
    }

    public function testExcluirWebhook(): void
    {
        new ExcluirWebhook(parent::$context)->handle(1);

        $this->addToAssertionCount(1);
    }

    /** @return CadastrarWebhookRequest */
    private function webhook(): array
    {
        return [
            'evento' => 1,
            'cooperadoId' => 'Testes',
            'url' => 'https://play.svix.com/in/e_zP7ZsNZl3RjJ3922xKM4Y8WTa1Q/',
        ];
    }
}
