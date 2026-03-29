<?php

declare(strict_types=1);

namespace Ailos\Sdk\Exceptions;

final class InvalidCredentialsException extends AuthenticationException
{
    public static function invalidClientCredentials(): self
    {
        return new self('Invalid Consumer Key or Consumer Secret provided.');
    }

    public static function invalidCooperadoCredentials(): self
    {
        return new self('Invalid cooperado credentials: check CodigoCooperativa, CodigoConta or Senha.');
    }
}
