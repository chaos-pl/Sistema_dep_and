<?php

namespace App\Http\Controllers\Psicologo;

use App\Http\Controllers\Controller;
use App\Models\AnalisisNlp;
use App\Services\AnalisisNlpQueueService;
use RealRashid\SweetAlert\Facades\Alert;

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
            ->where('estado_analisis', 'completado')
            ->count();

        $totalPendientes = AnalisisNlp::where('estado_analisis', '!=', 'completado')->count();

        $promedioConfianza = AnalisisNlp::where('requiere_atencion', true)
            ->where('estado_analisis', 'completado')->avg('confianza_hibrida');

        $pendientes = AnalisisNlp::with('estudiante.persona')
            ->where('estado_analisis', '!=', 'completado')
            ->latest()
            ->paginate(10, ['*'], 'pendientes_page');

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

    public function reanalizar(AnalisisNlp $analisisNlp, AnalisisNlpQueueService $queue)
    {
        $queued = $queue->solicitar($analisisNlp);
        Alert::info('Análisis en cola', $queued
            ? 'Se procesará en segundo plano. Actualiza el listado para consultar su estado.'
            : 'Esta entrada ya tiene un análisis en curso.');

        return redirect()->route('analisis.index');
    }

    public function reanalizarPendientes(AnalisisNlpQueueService $queue)
    {
        $entries = AnalisisNlp::whereIn('estado_analisis', ['fallido', 'legacy'])
            ->oldest()->take(20)->get();
        $queued = 0;
        foreach ($entries as $entry) {
            $queued += (int) $queue->solicitar($entry);
        }
        Alert::info('Procesamiento en segundo plano', "{$queued} entradas enviadas a la cola. Las entradas ya en curso no se duplican.");

        return redirect()->route('analisis.index');
    }
}
