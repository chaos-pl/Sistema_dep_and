<?php

namespace App\Http\Controllers\Estudiante;

use App\Http\Controllers\Controller;
use App\Http\Requests\Estudiante\StoreDiarioRequest;
use App\Models\AnalisisNlp;
use App\Services\AnalisisNlpQueueService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;
use Throwable;

class DiarioController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('persona', 'estudiante', 'persona.estudiante');

        $estudiante = $user->estudiante ?? $user->persona?->estudiante;

        if (! $estudiante) {
            Alert::warning(
                'Expediente incompleto',
                'Tu cuenta no tiene un expediente de estudiante vinculado.'
            );

            return redirect()->route('estudiante.dashboard');
        }

        $entradas = AnalisisNlp::where('codigo_anonimo', $estudiante->codigo_anonimo)
            ->latest()
            ->paginate(10);

        return view('Diario.index', compact('user', 'estudiante', 'entradas'));
    }

    public function store(StoreDiarioRequest $request, AnalisisNlpQueueService $queue)
    {
        $user = Auth::user()->load('persona', 'estudiante', 'persona.estudiante');

        $estudiante = $user->estudiante ?? $user->persona?->estudiante;

        if (! $estudiante) {
            Alert::error(
                'No disponible',
                'Tu cuenta no tiene un expediente de estudiante vinculado.'
            );

            return redirect()->route('estudiante.dashboard');
        }

        try {
            DB::transaction(function () use ($request, $estudiante, $queue) {
                $entry = AnalisisNlp::create([
                    'codigo_anonimo' => $estudiante->codigo_anonimo,
                    'texto_ingresado' => $request->validated('texto_ingresado'),
                    'etiqueta_roberta' => 'pendiente',
                    'score_confianza' => 0,
                    'requiere_atencion' => false,
                    'estado_analisis' => 'fallido',
                ]);
                $queue->solicitar($entry);
            });
        } catch (Throwable) {
            Log::warning('No se pudo guardar la entrada y su trabajo IA', ['codigo' => 'persistencia_no_disponible']);
            Alert::error('Entrada no guardada', 'No se pudo guardar el registro. Intenta nuevamente cuando el servicio esté disponible.');

            return redirect()->route('diario.index');
        }

        Alert::success('Entrada guardada', 'Tu registro fue guardado. El análisis se realizará en segundo plano.');

        return redirect()->route('diario.index');
    }
}
