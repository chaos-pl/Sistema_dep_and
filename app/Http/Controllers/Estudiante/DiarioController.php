<?php

namespace App\Http\Controllers\Estudiante;

use App\Http\Controllers\Controller;
use App\Http\Requests\Estudiante\StoreDiarioRequest;
use App\Models\AnalisisNlp;
use App\Services\PrometeoIaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;
use Throwable;

class DiarioController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('persona', 'estudiante', 'persona.estudiante');

        $estudiante = $user->estudiante ?? $user->persona?->estudiante;

        if (!$estudiante) {
            Alert::warning(
                'Expediente incompleto',
                'Tu cuenta no tiene un expediente de estudiante vinculado.'
            );

            return redirect()->route('estudiante.dashboard');
        }

        $entradas = AnalisisNlp::where('codigo_anonimo', $estudiante->codigo_anonimo)
            ->latest()
            ->paginate(10);

        return view('diario.index', compact('user', 'estudiante', 'entradas'));
    }

    public function store(StoreDiarioRequest $request, PrometeoIaService $ia)
    {
        $user = Auth::user()->load('persona', 'estudiante', 'persona.estudiante');

        $estudiante = $user->estudiante ?? $user->persona?->estudiante;

        if (!$estudiante) {
            Alert::error(
                'No disponible',
                'Tu cuenta no tiene un expediente de estudiante vinculado.'
            );

            return redirect()->route('estudiante.dashboard');
        }

        $validated = $request->validated();

        $textoIngresado = $validated['texto_ingresado'];

        /*
        |--------------------------------------------------------------------------
        | Diario emocional
        |--------------------------------------------------------------------------
        | En el diario analizamos principalmente el texto escrito.
        | No usamos los últimos puntajes PHQ-9/GAD-7 porque si el estudiante
        | tuvo una evaluación previa alta, todas sus entradas aparecerían como
        | "requiere atención", aunque el texto sea positivo o neutro.
        |--------------------------------------------------------------------------
        */

        $phq9 = 0;
        $gad7 = 0;

        try {
            $resultadoIa = $ia->evaluar(
                $textoIngresado,
                $phq9,
                $gad7
            );

            $requiereAtencion = (bool) ($resultadoIa['requiere_atencion'] ?? false);

            $etiquetaModelo = $resultadoIa['hibrido']['etiqueta_final'] ?? 'SIN_RESULTADO';

            /*
            |--------------------------------------------------------------------------
            | Normalización de etiqueta
            |--------------------------------------------------------------------------
            | No se agrega detección local. Solo se evita guardar una contradicción
            | visual cuando la API indica requiere_atencion = true pero la etiqueta
            | final viene como SIN_RIESGO.
            |--------------------------------------------------------------------------
            */

            if ($requiereAtencion && $etiquetaModelo === 'SIN_RIESGO') {
                $etiquetaModelo = 'RIESGO_DEPRESIVO';
            }

            AnalisisNlp::create([
                'codigo_anonimo' => $estudiante->codigo_anonimo,
                'texto_ingresado' => $textoIngresado,
                'etiqueta_roberta' => $etiquetaModelo,
                'score_confianza' => round((float) ($resultadoIa['hibrido']['confianza'] ?? 0), 4),
                'requiere_atencion' => $requiereAtencion,
            ]);

            Alert::success(
                'Entrada guardada',
                'Tu registro emocional fue guardado y analizado correctamente.'
            );

            return redirect()->route('diario.index');

        } catch (Throwable $e) {
            Log::error('Error al analizar diario emocional con API IA PROMETEO', [
                'codigo_anonimo' => $estudiante->codigo_anonimo,
                'texto_ingresado' => $textoIngresado,
                'error' => $e->getMessage(),
            ]);

            AnalisisNlp::create([
                'codigo_anonimo' => $estudiante->codigo_anonimo,
                'texto_ingresado' => $textoIngresado,
                'etiqueta_roberta' => 'pendiente',
                'score_confianza' => 0.0000,
                'requiere_atencion' => false,
            ]);

            Alert::warning(
                'Entrada guardada',
                'Tu registro emocional fue guardado, pero el análisis automático quedó pendiente.'
            );

            return redirect()->route('diario.index');
        }
    }
}
