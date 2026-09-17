<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use App\Models\Grupo;
use Illuminate\Support\Facades\Auth;

class GrupoController extends Controller
{
    public function index()
    {
        $tutor = $this->tutorAutenticado();

        $grupos = Grupo::visibleToTutor($tutor)
            ->withCount('estudiantes')
            ->latest()
            ->paginate(10);

        return view('tutor.grupos.index', compact('grupos'));
    }

    public function seguimiento()
    {
        $tutor = $this->tutorAutenticado();
        $estudiantes = Estudiante::conSeguimientoPendiente()
            ->whereIn('grupo_id', Grupo::visibleToTutor($tutor)->select('grupos.id'))
            ->with(['persona', 'grupo'])
            ->orderBy('grupo_id')->orderBy('id')->paginate(15);

        return view('tutor.seguimiento', compact('estudiantes'));
    }

    public function show(Grupo $grupo)
    {
        $tutor = $this->tutorAutenticado();

        abort_unless(Grupo::visibleToTutor($tutor)->whereKey($grupo->id)->exists(), 403, 'No puedes acceder a un grupo que no te pertenece.');

        $grupo->load([
            'estudiantes.persona.user',
            'estudiantes.latestDass21.evaluacion.alerta',
        ]);
        $pendingIds = Estudiante::conSeguimientoPendiente()->whereIn('id', $grupo->estudiantes->modelKeys())->pluck('id');
        $grupo->estudiantes->each(fn ($student) => $student->setAttribute('seguimientos_pendientes', $pendingIds->contains($student->id)));

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
