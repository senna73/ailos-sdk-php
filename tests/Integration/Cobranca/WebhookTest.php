<?php

declare(strict_types=1);

namespace Ailos\Sdk\Tests\Integration\Cobranca;

use Ailos\Sdk\Cobranca\Endpoints\Webhook\CadastrarWebhook;
use Ailos\Sdk\Cobranca\Endpoints\Webhook\ConsultarWebhook;
use Ailos\Sdk\Cobranca\Endpoints\Webhook\ExcluirWebhook;
use Ailos\Sdk\Cobranca\Endpoints\Webhook\ListarWebhooks;
use Ailos\Sdk\Tests\CobrancaTestCase;

/**
 * @phpstan-import-type CadastrarWebhookRequest from CadastrarWebhook
 */
class WebhookTest extends CobrancaTestCase
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
