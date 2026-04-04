<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $persona = $user->persona;

        abort_unless($persona && $persona->tutor, 403, 'No existe un tutor vinculado a este usuario.');

        $tutor = $persona->tutor;

        $grupos = $tutor->grupos()
            ->withCount('estudiantes')
            ->get()
            ->map(function ($grupo) {
                $grupo->completadas = 0;
                $grupo->abandonadas = 0;
                return $grupo;
            });

        $totalGrupos = $grupos->count();
        $totalEstudiantes = $grupos->sum('estudiantes_count');
        $completadas = 0;
        $abandonadas = 0;

        return view('tutor.dashboard', compact(
            'totalGrupos',
            'totalEstudiantes',
            'completadas',
            'abandonadas',
            'grupos'
        ));
    }
}
