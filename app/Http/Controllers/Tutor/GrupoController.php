<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Grupo;
use Illuminate\Support\Facades\Auth;

class GrupoController extends Controller
{
    public function index()
    {
        $tutor = $this->tutorAutenticado();

        $grupos = $tutor->grupos()
            ->withCount('estudiantes')
            ->latest()
            ->paginate(10);

        return view('tutor.grupos.index', compact('grupos'));
    }

    public function show(Grupo $grupo)
    {
        $tutor = $this->tutorAutenticado();

        abort_unless((int) $grupo->tutor_id === (int) $tutor->id, 403, 'No puedes acceder a un grupo que no te pertenece.');

        $grupo->load([
            'estudiantes.persona.user'
        ]);

        return view('tutor.grupos.show', compact('grupo'));
    }

    private function tutorAutenticado()
    {
        $user = Auth::user();
        $persona = $user->persona;

        abort_unless($persona && $persona->tutor, 403, 'No existe un tutor vinculado a este usuario.');

        return $persona->tutor;
    }
}
