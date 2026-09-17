<?php

namespace App\Http\Controllers\Psicologo;

use App\Http\Controllers\Controller;
use App\Models\CasoAtencion;
use App\Models\Psicologo;
use App\Services\CasoAtencionService;
use Illuminate\Http\Request;

class CasoAtencionController extends Controller
{
    private function professional(Request $request): void
    {
        abort_unless($request->user()->persona?->psicologo, 403, 'Expediente profesional pendiente.');
    }

    public function index(Request $request)
    {
        $this->professional($request);
        $filters = $request->validate([
            'estado' => ['nullable', 'in:pendiente,asignado,seguimiento,cerrado'],
            'origen' => ['nullable', 'in:evaluacion,diario'],
            'responsable' => ['nullable', 'in:mios,sin_asignar'],
        ]);
        $base = CasoAtencion::visibleTo($request->user());
        $counts = (clone $base)->selectRaw('estado, count(*) as total')->groupBy('estado')->pluck('total', 'estado');
        $casos = $base->with(['psicologo.persona', 'evaluacion.instrumento', 'evaluacion.estudiante.persona', 'analisisNlp.estudiante.persona'])
            ->when($filters['estado'] ?? null, fn ($q, $value) => $q->where('estado', $value))
            ->when(($filters['origen'] ?? null) === 'evaluacion', fn ($q) => $q->whereNotNull('evaluacion_id'))
            ->when(($filters['origen'] ?? null) === 'diario', fn ($q) => $q->whereNotNull('analisis_nlp_id'))
            ->when(($filters['responsable'] ?? null) === 'mios', fn ($q) => $q->where('psicologo_id', $request->user()->persona->psicologo->id))
            ->when(($filters['responsable'] ?? null) === 'sin_asignar', fn ($q) => $q->whereNull('psicologo_id'))
            ->latest('updated_at')->latest('id')->paginate(20)->withQueryString();

        return view('psicologo.casos.index', compact('casos', 'counts'));
    }

    public function show(Request $request, CasoAtencion $caso)
    {
        $this->professional($request);
        abort_unless(CasoAtencion::visibleTo($request->user())->whereKey($caso->id)->exists(), 403);
        $caso->load(['psicologo.persona', 'evaluacion.instrumento', 'evaluacion.estudiante.persona', 'analisisNlp.estudiante.persona']);
        $seguimientos = $caso->seguimientos()->with(['autor', 'responsable.persona'])->latest('id')->paginate(20);
        $psicologos = Psicologo::with('persona.user')->get()->filter(function ($psych) use ($caso) {
            $user = $psych->persona?->user;

            return $user && $user->hasRole('psicologo') && $user->can('diagnosticos.crear')
                && CasoAtencion::visibleTo($user)->whereKey($caso->id)->exists();
        });

        return view('psicologo.casos.show', compact('caso', 'seguimientos', 'psicologos'));
    }

    public function update(Request $request, CasoAtencion $caso, CasoAtencionService $service)
    {
        $this->professional($request);
        $service->authorize($caso, $request->user());
        $data = $request->validate([
            'version' => ['required', 'integer', 'min:0'],
            'accion' => ['required', 'in:asignar,nota,cerrar,reabrir'],
            'nota' => ['required', 'string', 'max:3000'],
            'psicologo_id' => ['required_if:accion,asignar', 'nullable', 'integer', 'exists:psicologos,id'],
        ]);
        $service->change($caso, $request->user(), (int) $data['version'], $data['accion'], $data['nota'], isset($data['psicologo_id']) ? (int) $data['psicologo_id'] : null);

        return redirect()->route('psicologo.casos.show', $caso)->with('status', 'El cambio se guardó en el historial del caso.');
    }
}
