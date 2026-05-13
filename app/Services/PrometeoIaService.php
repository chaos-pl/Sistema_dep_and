<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class PrometeoIaService
{
    public function evaluar(string $texto, int $phq9, int $gad7 = 0): array
    {
        $url = config('services.prometeo_ia.url');

        if (!$url) {
            throw new RuntimeException('No está configurada la URL de la API de IA de PROMETEO.');
        }

        $response = Http::timeout(config('services.prometeo_ia.timeout', 60))
            ->acceptJson()
            ->withHeaders([
                'ngrok-skip-browser-warning' => 'true',
            ])
            ->post($url, [
                'texto' => $texto,
                'phq9' => $phq9,
                'gad7' => $gad7,
            ]);

        if (!$response->successful()) {
            Log::error('Error al consultar API PROMETEO IA', [
                'url' => $url,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new RuntimeException(
                'No se pudo obtener respuesta válida de la API de IA. Status: '
                . $response->status()
                . ' Body: '
                . $response->body()
            );
        }

        return $response->json();
    }
}
