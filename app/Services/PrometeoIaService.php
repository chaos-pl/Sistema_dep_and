<?php

namespace App\Services;

use App\Exceptions\PrometeoIaException;
use Illuminate\Support\Facades\Http;
use Throwable;

class PrometeoIaService
{
    public function evaluar(#[\SensitiveParameter] string $texto, int $phq9, int $gad7 = 0): array
    {
        $url = config('services.prometeo_ia.url');
        if (! $url) {
            throw new PrometeoIaException('sin_configuracion');
        }
        try {
            $response = Http::withoutRedirecting()->connectTimeout(10)
                ->timeout(max(1, min(60, (int) config('services.prometeo_ia.timeout', 60))))
                ->acceptJson()->withHeaders(['ngrok-skip-browser-warning' => 'true'])
                ->post($url, ['texto' => $texto, 'phq9' => $phq9, 'gad7' => $gad7]);
        } catch (Throwable) {
            throw new PrometeoIaException('conexion', true);
        }
        if (! $response->successful()) {
            throw new PrometeoIaException('http_'.$response->status(), $response->serverError() || in_array($response->status(), [408, 429], true));
        }
        $data = $response->json();
        $label = data_get($data, 'hibrido.etiqueta_final');
        $code = data_get($data, 'hibrido.codigo');
        $confidence = data_get($data, 'hibrido.confianza');
        $risk = data_get($data, 'beto.prob_riesgo_depresivo');
        $noRisk = data_get($data, 'beto.prob_sin_riesgo');
        $attention = data_get($data, 'requiere_atencion');
        $probability = fn ($value) => (is_int($value) || is_float($value)) && is_finite((float) $value) && $value >= 0 && $value <= 1;
        if (data_get($data, 'status') !== 'ok' || ! is_bool($attention)
            || ! in_array($code, [0, 1], true)
            || $label !== ($code === 1 ? 'RIESGO_DEPRESIVO' : 'SIN_RIESGO')
            || ! $probability($confidence) || ! $probability($risk) || ! $probability($noRisk)
            || abs($risk + $noRisk - 1) > 0.001) {
            throw new PrometeoIaException('contrato_invalido');
        }

        // Lista permitida: nunca persistir entrada, texto limpio o cuerpo completo.
        return [
            'probabilidad_beto' => $risk,
            'etiqueta_hibrida' => $label,
            'confianza_hibrida' => $confidence,
            'requiere_atencion' => $attention,
        ];
    }
}
