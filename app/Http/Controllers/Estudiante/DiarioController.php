<?php

namespace App\Http\Controllers\Estudiante;

use App\Http\Controllers\Controller;
use App\Http\Requests\Estudiante\StoreDiarioRequest;
use App\Models\AnalisisNlp;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class DiarioController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('persona', 'estudiante');

        $estudiante = $user->estudiante;

        if (!$estudiante) {
            Alert::warning('Expediente incompleto', 'Tu cuenta no tiene un expediente de estudiante vinculado.');
            return redirect()->route('estudiante.dashboard');
        }

        $entradas = AnalisisNlp::where('codigo_anonimo', $estudiante->codigo_anonimo)
            ->latest()
            ->paginate(10);

        return view('diario.index', compact('user', 'estudiante', 'entradas'));
    }

    public function store(StoreDiarioRequest $request)
    {
        $user = Auth::user()->load('persona', 'estudiante');

        $estudiante = $user->estudiante;

        if (!$estudiante) {
            Alert::error('No disponible', 'Tu cuenta no tiene un expediente de estudiante vinculado.');
            return redirect()->route('estudiante.dashboard');
        }

        AnalisisNlp::create([
            'codigo_anonimo' => $estudiante->codigo_anonimo,
            'texto_ingresado' => $request->texto_ingresado,
            'etiqueta_roberta' => 'pendiente',
            'score_confianza' => 0.0000,
            'requiere_atencion' => false,
        ]);

        Alert::success('Entrada guardada', 'Tu registro emocional fue guardado correctamente.');

        return redirect()->route('diario.index');
    }
}
