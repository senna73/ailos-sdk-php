<?php

declare(strict_types=1);

namespace Ailos\Sdk\Auth\Callback;

use Ailos\Sdk\Exceptions\AuthenticationException;

class CallbackPayload
{
    public function __construct(
        private readonly string $code,
        private readonly string $state = '',
    ) {
        $this->validate();
    }

    public static function fromJson(string $json): static
    {
        $data = json_decode($json, associative: true);

        if (!is_array($data)) {
            throw AuthenticationException::withMessage(
                'Invalid callback payload: could not parse JSON body.'
            );
        }

        return static::fromArray($data);
    }

    public static function fromArray(array $data): static
    {
        return new static(
            code:  $data['code']  ?? '',
            state: $data['state'] ?? '',
        );
    }

    public static function fromGlobals(): static
    {
        // Tenta ler do corpo JSON da requisição
        $body = file_get_contents('php://input');

        if (!empty($body)) {
            return static::fromJson($body);
        }

        // Fallback para $_POST
        return static::fromArray($_POST);
    }

    public function code(): string
    {
        return $this->code;
    }

    public function state(): string
    {
        return $this->state;
    }

    private function validate(): void
    {
        if (empty(trim($this->code))) {
            throw AuthenticationException::withMessage(
                'Invalid callback payload: missing required field "code".'
            );
        }
    }
}
