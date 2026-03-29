<?php

declare(strict_types=1);

namespace Ailos\Sdk\Collection\Auth\Credentials;

use Ailos\Sdk\Exceptions\InvalidCredentialsException;

class CooperadoCredentials
{
    public function __construct(
        private readonly string $urlCallback,
        private readonly string $ailosApiKeyDeveloper,
        private readonly string $codigoCooperativa,
        private readonly string $codigoConta,
        private readonly string $senha,
        private readonly string $state = 'default-state',
    ) {
        $this->validate();
    }

    public function urlCallback(): string
    {
        return $this->urlCallback;
    }

    public function ailosApiKeyDeveloper(): string
    {
        return $this->ailosApiKeyDeveloper;
    }

    public function codigoCooperativa(): string
    {
        return $this->codigoCooperativa;
    }

    public function codigoConta(): string
    {
        return $this->codigoConta;
    }

    public function senha(): string
    {
        return $this->senha;
    }

    public function state(): string
    {
        return $this->state;
    }

    /**
     * @return array<string, string>
     */
    public function toLoginPayload(): array
    {
        return [
            'Login.CodigoCooperativa' => $this->codigoCooperativa,
            'Login.CodigoConta'       => $this->codigoConta,
            'Login.Senha'             => $this->senha,
        ];
    }

    /**
     * @return array<string, string>
     */
    public function toAuthIdPayload(): array
    {
        return [
            'urlCallback'          => $this->urlCallback,
            'ailosApiKeyDeveloper' => $this->ailosApiKeyDeveloper,
            'state'                => $this->state,
        ];
    }

    private function validate(): void
    {
        $requiredFields = [
            'urlCallback'          => $this->urlCallback,
            'ailosApiKeyDeveloper' => $this->ailosApiKeyDeveloper,
            'codigoCooperativa'    => $this->codigoCooperativa,
            'codigoConta'          => $this->codigoConta,
            'senha'                => $this->senha,
        ];

        foreach ($requiredFields as $value) {
            if (empty(trim($value))) {
                throw InvalidCredentialsException::invalidCooperadoCredentials();
            }
        }
    }
}
