<?php

declare(strict_types=1);

namespace Ailos\Sdk\Auth\Callback;

use Ailos\Sdk\AilosSdk;

class CallbackHandler
{
    public function __construct(
        private readonly AilosSdk $sdk,
    ) {
    }

    /**
     * Processa o callback a partir do corpo da requisição atual.
     * Ideal para PHP puro — lê automaticamente de php://input ou $_POST.
     */
    public function handleFromGlobals(): CallbackPayload
    {
        $payload = CallbackPayload::fromGlobals();

        $this->process($payload);

        return $payload;
    }

    /**
     * Processa o callback a partir de uma string JSON.
     * Ideal para frameworks que expõem o corpo da requisição como string.
     */
    public function handleFromJson(string $json): CallbackPayload
    {
        $payload = CallbackPayload::fromJson($json);

        $this->process($payload);

        return $payload;
    }

    /**
     * Processa o callback a partir de um array.
     * Ideal para Laravel ($request->all()), Symfony, Slim, etc.
     */
    public function handleFromArray(array $data): CallbackPayload
    {
        $payload = CallbackPayload::fromArray($data);

        $this->process($payload);

        return $payload;
    }

    private function process(CallbackPayload $payload): void
    {
        $this->sdk->handleCallback($payload->code());
    }
}
