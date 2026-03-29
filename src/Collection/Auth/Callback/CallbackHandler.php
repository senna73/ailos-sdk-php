<?php

declare(strict_types=1);

namespace Ailos\Sdk\Collection\Auth\Callback;

use Ailos\Sdk\Collection\Auth\Tokens\JwtToken;
use Ailos\Sdk\Storage\Contracts\TokenStoreInterface;
use Ailos\Sdk\Storage\TokenKeys;

readonly class CallbackHandler
{
    public function __construct(
        private TokenStoreInterface $tokenStore,
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
     *
     * @param array<string, mixed> $data
     */
    public function handleFromArray(array $data): CallbackPayload
    {
        $payload = CallbackPayload::fromArray($data);

        $this->process($payload);

        return $payload;
    }

    private function process(CallbackPayload $payload): void
    {
        $jwt = new JwtToken($payload->code());
        $this->tokenStore->set(TokenKeys::JWT, $jwt);
    }
}
