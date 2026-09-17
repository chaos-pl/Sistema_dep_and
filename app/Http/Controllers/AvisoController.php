<?php

namespace App\Http\Controllers;

use App\Models\Aviso;
use App\Models\CasoAtencion;
use App\Models\Evaluacion;
use Illuminate\Http\Request;

class AvisoController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate(['estado' => ['nullable', 'in:todos,pendientes']]);
        $avisos = Aviso::where('user_id', $request->user()->id)
            ->when(($data['estado'] ?? '') === 'pendientes', fn ($q) => $q->whereNull('leido_at'))
            ->latest('id')->paginate(20)->withQueryString();

        return view('avisos.index', compact('avisos'));
    }

    public function update(Request $request, Aviso $aviso)
    {
        abort_unless($aviso->user_id === $request->user()->id, 404);
        $data = $request->validate(['leido' => ['required', 'boolean']]);
        $aviso->update(['leido_at' => $data['leido'] ? now() : null]);

        return back();
    }

    public function open(Request $request, Aviso $aviso)
    {
        $user = $request->user();
        abort_unless($aviso->user_id === $user->id, 404);
        if ($aviso->tipo === 'caso') {
            abort_unless($user->hasRole('psicologo') && $user->persona?->psicologo
                && CasoAtencion::visibleTo($user)->whereKey($aviso->caso_atencion_id)->exists(), 403);
            $url = route('psicologo.casos.show', $aviso->caso_atencion_id);
        } else {
            $evaluation = $aviso->evaluacion;
            $this->authorizeFeedback($request, $evaluation);
            $url = route('retroalimentacion.show', $evaluation);
        }
        $aviso->update(['leido_at' => now()]);

        return redirect($url);
    }

    private function authorizeFeedback(Request $request, Evaluacion $evaluacion): void
    {
        abort_unless($request->user()->hasRole('estudiante') && $request->user()->can('retroalimentacion.ver.propia')
            && $request->user()->persona?->estudiante?->codigo_anonimo === $evaluacion->codigo_anonimo, 403);
    }

    public function feedback(Request $request, Evaluacion $evaluacion)
    {
        $this->authorizeFeedback($request, $evaluacion);
        $feedback = $evaluacion->diagnostico?->retroalimentacion_estudiante;
        abort_if(trim((string) $feedback) === '', 404);

        return view('avisos.feedback', [
            'feedback' => $feedback, 'instrumento' => $evaluacion->instrumento->acronimo,
            'fecha' => $evaluacion->created_at,
        ]);
    }
}
