<?php

namespace App\Http\Controllers\Psicologo;

use App\Http\Controllers\Controller;
use App\Models\AnalisisNlp;
use App\Services\PrometeoIaService;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;
use Throwable;

class AnalisisNlpController extends Controller
{
    public function index()
    {
        $analisisRiesgo = AnalisisNlp::with('estudiante.persona', 'estudiante.grupo')
            ->where('requiere_atencion', true)
            ->latest()
            ->paginate(10);

        $totalRiesgo = AnalisisNlp::where('requiere_atencion', true)->count();
        $totalAnalisis = AnalisisNlp::count();
        $totalSinRiesgo = AnalisisNlp::where('requiere_atencion', false)
            ->where('etiqueta_roberta', '!=', 'pendiente')
            ->count();

        $totalPendientes = AnalisisNlp::where('etiqueta_roberta', 'pendiente')->count();

        $promedioConfianza = AnalisisNlp::where('requiere_atencion', true)
            ->avg('score_confianza');

        $pendientes = AnalisisNlp::with('estudiante.persona')
            ->where('etiqueta_roberta', 'pendiente')
            ->latest()
            ->take(10)
            ->get();

        return view('psicologo.analisis-nlp.index', compact(
            'analisisRiesgo',
            'totalRiesgo',
            'totalAnalisis',
            'totalSinRiesgo',
            'totalPendientes',
            'promedioConfianza',
            'pendientes'
        ));
    }

    public function show(AnalisisNlp $analisisNlp)
    {
        $analisisNlp->load('estudiante.persona', 'estudiante.grupo');

        return view('psicologo.analisis-nlp.show', compact('analisisNlp'));
    }

    public function reanalizar(AnalisisNlp $analisisNlp, PrometeoIaService $ia)
    {
        try {
            $resultadoIa = $ia->evaluar(
                $analisisNlp->texto_ingresado,
                0,
                0
            );

            $analisisNlp->update([
                'etiqueta_roberta' => $resultadoIa['hibrido']['etiqueta_final'] ?? 'SIN_RESULTADO',
                'score_confianza' => round((float) ($resultadoIa['hibrido']['confianza'] ?? 0), 4),
                'requiere_atencion' => (bool) ($resultadoIa['requiere_atencion'] ?? false),
            ]);

            Alert::success('Análisis actualizado', 'La entrada fue reanalizada correctamente.');

        } catch (Throwable $e) {
            Log::error('Error al reanalizar entrada NLP', [
                'analisis_nlp_id' => $analisisNlp->id,
                'error' => $e->getMessage(),
            ]);

            Alert::error(
                'No se pudo reanalizar',
                'Verifica que la API de IA esté activa y que la URL de ngrok sea correcta.'
            );
        }

        return redirect()->route('analisis.index');
    }

    public function reanalizarPendientes(PrometeoIaService $ia)
    {
        $pendientes = AnalisisNlp::where('etiqueta_roberta', 'pendiente')
            ->latest()
            ->take(20)
            ->get();

        if ($pendientes->isEmpty()) {
            Alert::info('Sin pendientes', 'No hay entradas pendientes por reanalizar.');
            return redirect()->route('analisis.index');
        }

        $procesadas = 0;
        $fallidas = 0;

        foreach ($pendientes as $analisis) {
            try {
                $resultadoIa = $ia->evaluar(
                    $analisis->texto_ingresado,
                    0,
                    0
                );

                $analisis->update([
                    'etiqueta_roberta' => $resultadoIa['hibrido']['etiqueta_final'] ?? 'SIN_RESULTADO',
                    'score_confianza' => round((float) ($resultadoIa['hibrido']['confianza'] ?? 0), 4),
                    'requiere_atencion' => (bool) ($resultadoIa['requiere_atencion'] ?? false),
                ]);

                $procesadas++;

            } catch (Throwable $e) {
                $fallidas++;

                Log::error('Error al reanalizar pendiente NLP', [
                    'analisis_nlp_id' => $analisis->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($procesadas > 0 && $fallidas === 0) {
            Alert::success(
                'Reanálisis completado',
                "Se reanalizaron {$procesadas} entradas pendientes correctamente."
            );
        } elseif ($procesadas > 0 && $fallidas > 0) {
            Alert::warning(
                'Reanálisis parcial',
                "Se reanalizaron {$procesadas} entradas, pero {$fallidas} fallaron."
            );
        } else {
            Alert::error(
                'No se pudo reanalizar',
                'Ninguna entrada pudo ser procesada. Verifica que la API esté activa.'
            );
        }

        return redirect()->route('analisis.index');
    }
}
