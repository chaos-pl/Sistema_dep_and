<?php

namespace App\Http\Controllers\Psicologo;

use App\Http\Controllers\Controller;
use App\Models\Evaluacion;
use App\Models\Grupo;
use App\Services\Dass21CoverageService;
use Illuminate\Http\Request;

class TamizajeController extends Controller
{
    public function index(Request $request, Dass21CoverageService $coverage)
    {
        abort_unless($request->user()->persona?->psicologo, 403, 'Expediente profesional pendiente.');
        $filters = $request->validate([
            'instrumento' => ['nullable', 'in:DASS21,PHQ9,GAD7'],
            'estado' => ['nullable', 'in:pendiente,atendida,sin_alerta,seguimiento,cerrado'],
            'grupo_id' => ['nullable', 'integer', 'exists:grupos,id'],
            'codigo_anonimo' => ['nullable', 'string', 'max:255'],
        ]);
        $periodo = $coverage->period($request);
        $evaluaciones = Evaluacion::with(['instrumento', 'estudiante.persona', 'dass21', 'resultadoClinico', 'alerta', 'diagnostico', 'casoAtencion'])
            ->where('estado', 'completada')->whereBetween('created_at', $periodo)
            ->when($filters['instrumento'] ?? null, fn ($q, $instrument) => $q->whereHas('instrumento', fn ($i) => $i->whereRaw('UPPER(REPLACE(acronimo, ?, ?)) = ?', ['-', '', $instrument])))
            ->when($filters['grupo_id'] ?? null, fn ($q, $id) => $q->whereHas('estudiante', fn ($s) => $s->where('grupo_id', $id)))
            ->when($filters['codigo_anonimo'] ?? null, fn ($q, $code) => $q->where('codigo_anonimo', $code))
            ->when(($filters['estado'] ?? null) === 'pendiente', fn ($q) => $q->whereHas('alerta', fn ($a) => $a->whereIn('estado', ['generada', 'asignada_psicologo'])))
            ->when(($filters['estado'] ?? null) === 'atendida', fn ($q) => $q->whereHas('diagnostico'))
            ->when(in_array($filters['estado'] ?? null, ['seguimiento', 'cerrado'], true), fn ($q) => $q->whereHas('casoAtencion', fn ($c) => $c->where('estado', $filters['estado'])))
            ->when(($filters['estado'] ?? null) === 'sin_alerta', fn ($q) => $q->doesntHave('alerta'))
            ->latest()->latest('id')->paginate(20)->withQueryString();

        $grupos = Grupo::orderBy('nombre')->get(['id', 'nombre']);

        return view('psicologo.tamizajes.index', compact('evaluaciones', 'periodo', 'grupos'));
    }

    public function show(Request $request, Evaluacion $evaluacion)
    {
        abort_unless($request->user()->persona?->psicologo, 403, 'Expediente profesional pendiente.');
        abort_unless($evaluacion->estado === 'completada', 404);
        $evaluacion->load(['instrumento', 'estudiante.persona', 'dass21.answers.question', 'resultadoClinico', 'respuestas', 'diagnostico', 'alerta']);
        $historial = Evaluacion::with(['instrumento', 'dass21', 'resultadoClinico'])
            ->where('codigo_anonimo', $evaluacion->codigo_anonimo)->where('estado', 'completada')
            ->latest()->latest('id')->paginate(10);

        return view('psicologo.tamizajes.show', compact('evaluacion', 'historial'));
    }
}
