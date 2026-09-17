<?php

namespace App\Exceptions;

use RuntimeException;

class PrometeoIaException extends RuntimeException
{
    public function __construct(public readonly string $reason, public readonly bool $retryable = false)
    {
        // No encadenar excepciones HTTP: pueden contener texto, cuerpos o secretos.
        parent::__construct('Análisis IA no disponible: '.$reason);
    }
}
