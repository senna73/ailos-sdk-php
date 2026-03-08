<?php

declare(strict_types=1);

namespace Ailos\Sdk\Exceptions;

class InvalidCredentialsException extends AuthenticationException
{
    public static function invalidClientCredentials(): static
    {
        return new static('Invalid Consumer Key or Consumer Secret provided.');
    }

    public static function invalidCooperadoCredentials(): static
    {
        return new static('Invalid cooperado credentials: check CodigoCooperativa, CodigoConta or Senha.');
    }
}
