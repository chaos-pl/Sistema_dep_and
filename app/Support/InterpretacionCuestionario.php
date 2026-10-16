<?php

namespace App\Support;

final class InterpretacionCuestionario
{
    public static function interpretar(
        string $acronimo,
        ?int $puntaje
    ): ?array {
        $codigo = strtoupper(
            str_replace(['-', '_', ' '], '', $acronimo)
        );

        $maximo = match ($codigo) {
            'PHQ9' => 27,
            'GAD7' => 21,
            default => null,
        };

        if (
            $maximo === null ||
            $puntaje === null ||
            $puntaje < 0 ||
            $puntaje > $maximo
        ) {
            return null;
        }

        $nivel = match (true) {
            $puntaje <= 4 => 'minimo',
            $puntaje <= 9 => 'leve',
            $puntaje <= 14 => 'moderado',
            $codigo === 'PHQ9' && $puntaje <= 19
            => 'moderadamente_grave',
            default => 'grave',
        };

        return match ($nivel) {
            'minimo' => [
                'etiqueta' => 'Síntomas mínimos',
                'clase' => 'risk-nulo',
            ],
            'leve' => [
                'etiqueta' => 'Síntomas leves',
                'clase' => 'risk-leve',
            ],
            'moderado' => [
                'etiqueta' => 'Síntomas moderados',
                'clase' => 'risk-moderado',
            ],
            'moderadamente_grave' => [
                'etiqueta' => 'Síntomas moderadamente graves',
                'clase' => 'risk-severo',
            ],
            'grave' => [
                'etiqueta' => 'Síntomas graves',
                'clase' => 'risk-severo',
            ],
        };
    }
}
