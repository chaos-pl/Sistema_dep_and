<?php

namespace App\Http\Controllers\Estudiante;

use App\Http\Controllers\Controller;
use App\Http\Requests\Estudiante\StoreEvaluacionRequest;
use App\Models\Evaluacion;
use App\Models\Instrumento;
use App\Models\ResultadoClinico;
use App\Models\Respuesta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class EvaluacionController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('persona.estudiante.grupo');

        $persona = $user->persona;
        $estudiante = $persona?->estudiante;

        if (!$persona || !$estudiante) {
            return view('estudiante.pendiente-expediente', [
                'titulo' => 'Asignación de grupo pendiente',
                'mensaje' => 'Tu registro existe, pero todavía no tienes expediente estudiantil completo para responder evaluaciones.',
                'estado' => 'sin_expediente',
            ]);
        }

        $instrumentos = Instrumento::whereIn('acronimo', ['PHQ9', 'GAD7', 'phq9', 'gad7'])
            ->orderBy('nombre')
            ->get();

        $evaluaciones = Evaluacion::with(['instrumento', 'resultadoClinico'])
            ->where('codigo_anonimo', $estudiante->codigo_anonimo)
            ->latest()
            ->get();

        $instrumentosDashboard = $instrumentos->map(function ($instrumento) use ($evaluaciones) {
            $ultima = $evaluaciones
                ->where('instrumento_id', $instrumento->id)
                ->sortByDesc('id')
                ->first();

            return (object) [
                'instrumento' => $instrumento,
                'evaluacion' => $ultima,
                'estado' => $ultima?->estado ?? 'pendiente',
                'puntaje_total' => $ultima?->resultadoClinico?->puntaje_total,
                'nivel_riesgo' => $ultima?->resultadoClinico?->nivel_riesgo,
            ];
        });

        return view('evaluaciones.index', compact(
            'user',
            'estudiante',
            'instrumentosDashboard'
        ));
    }

    public function aplicar(string $tipo)
    {
        $user = Auth::user()->load('persona.estudiante.grupo');

        $persona = $user->persona;
        $estudiante = $persona?->estudiante;

        if (!$persona || !$estudiante) {
            return view('estudiante.pendiente-expediente', [
                'titulo' => 'Asignación de grupo pendiente',
                'mensaje' => 'Tu expediente aún no está completo, por eso todavía no puedes responder evaluaciones.',
                'estado' => 'sin_expediente',
            ]);
        }

        $instrumento = Instrumento::whereRaw('LOWER(acronimo) = ?', [strtolower($tipo)])->firstOrFail();

        $preguntas = $this->obtenerPreguntas($instrumento->acronimo);

        return view('evaluaciones.aplicar', compact(
            'instrumento',
            'preguntas',
            'estudiante'
        ));
    }

    public function responder(StoreEvaluacionRequest $request, string $tipo)
    {
        $user = Auth::user()->load('persona.estudiante');

        $persona = $user->persona;
        $estudiante = $persona?->estudiante;

        if (!$persona || !$estudiante) {
            Alert::warning('Expediente pendiente', 'Tu expediente aún no está completo.');
            return redirect()->route('evaluaciones.index');
        }

        $instrumento = Instrumento::whereRaw('LOWER(acronimo) = ?', [strtolower($tipo)])->firstOrFail();
        $preguntas = $this->obtenerPreguntas($instrumento->acronimo);
        $respuestas = $request->input('respuestas', []);

        if (count($respuestas) !== count($preguntas)) {
            Alert::error('Formulario incompleto', 'Debes responder todas las preguntas.');
            return back()->withInput();
        }

        DB::transaction(function () use ($instrumento, $estudiante, $respuestas) {
            $evaluacion = Evaluacion::create([
                'codigo_anonimo' => $estudiante->codigo_anonimo,
                'instrumento_id' => $instrumento->id,
                'estado' => 'completada',
            ]);

            $puntajeTotal = 0;

            foreach ($respuestas as $numeroPregunta => $valor) {
                Respuesta::create([
                    'evaluacion_id' => $evaluacion->id,
                    'numero_pregunta' => (int) $numeroPregunta,
                    'valor' => (int) $valor,
                ]);

                $puntajeTotal += (int) $valor;
            }

            ResultadoClinico::create([
                'evaluacion_id' => $evaluacion->id,
                'puntaje_total' => $puntajeTotal,
                'nivel_riesgo' => $this->calcularNivelRiesgo($instrumento->acronimo, $puntajeTotal),
            ]);
        });

        Alert::success('Evaluación enviada', 'Tu evaluación fue registrada correctamente.');

        return redirect()->route('evaluaciones.index');
    }

    private function obtenerPreguntas(string $acronimo): array
    {
        $acronimo = strtoupper($acronimo);

        if ($acronimo === 'PHQ9') {
            return [
                1 => 'Poco interés o placer en hacer cosas',
                2 => 'Se ha sentido decaído(a), deprimido(a) o sin esperanzas',
                3 => 'Ha tenido dificultad para quedarse o permanecer dormido(a), o ha dormido demasiado',
                4 => 'Se ha sentido cansado(a) o con poca energía',
                5 => 'Sin apetito o ha comido en exceso',
                6 => 'Se ha sentido mal con usted mismo(a) – o que es un fracaso o que ha quedado mal con usted mismo(a) o con su familia',
                7 => 'Ha tenido dificultad para concentrarse en ciertas actividades, tales como leer el periódico o ver la televisión',
                8 => '¿Se ha movido o hablado tan lento que otras personas podrían haberlo notado? o lo contrario – muy inquieto(a) o agitado(a) que ha estado moviéndose mucho más de lo normal',
                9 => 'Pensamientos de que estaría mejor muerto(a) o de lastimarse de alguna manera',
            ];
        }

        if ($acronimo === 'GAD7') {
            return [
                1 => 'Se ha sentido nervioso(a), ansioso(a) o con los nervios de punta',
                2 => 'No ha sido capaz de parar o controlar su preocupación',
                3 => 'Se ha preocupado demasiado por motivos diferentes',
                4 => 'Ha tenido dificultad para relajarse',
                5 => 'Se ha sentido tan inquieto(a) que no ha podido quedarse quieto(a)',
                6 => 'Se ha molestado o irritado fácilmente',
                7 => 'Ha tenido miedo de que algo terrible fuera a pasar',
            ];
        }

        abort(404, 'Instrumento no soportado.');
    }

    private function calcularNivelRiesgo(string $acronimo, int $puntaje): string
    {
        $acronimo = strtoupper($acronimo);

        if ($acronimo === 'PHQ9') {
            return match (true) {
                $puntaje <= 4 => 'nulo',
                $puntaje <= 9 => 'leve',
                $puntaje <= 14 => 'moderado',
                $puntaje <= 19 => 'severo',
                default => 'severo',
            };
        }

        if ($acronimo === 'GAD7') {
            return match (true) {
                $puntaje <= 4 => 'nulo',
                $puntaje <= 9 => 'leve',
                $puntaje <= 14 => 'moderado',
                default => 'severo',
            };
        }

        return 'nulo';
    }
}
