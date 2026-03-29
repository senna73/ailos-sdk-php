<?php

declare(strict_types=1);

namespace Ailos\Sdk\Collection\Auth\Callback;

use Ailos\Sdk\Exceptions\AuthenticationException;

final readonly class CallbackPayload
{
    public function __construct(
        private string $code,
        private string $state = '',
    ) {
        $this->validate();
    }

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, associative: true);

        if (!is_array($data)) {
            throw AuthenticationException::withMessage(
                'Invalid callback payload: could not parse JSON body.'
            );
        }

        /** @var array<string, mixed> $data */
        return self::fromArray($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $code = isset($data['code']) && is_string($data['code'])
            ? $data['code']
            : '';

        $state = isset($data['state']) && is_string($data['state'])
            ? $data['state']
            : '';

        return new self(
            code: $code,
            state: $state,
        );
    }

    public static function fromGlobals(): self
    {
        // Tenta ler do corpo JSON da requisição
        $body = file_get_contents('php://input');

        if (!empty($body)) {
            return self::fromJson($body);
        }

        /** @var array<string, mixed> $post */
        $post = $_POST;

        return self::fromArray($post);
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
