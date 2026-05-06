<?php

namespace App\Http\Controllers\ControlEscolar;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\CicloEscolar;
use App\Models\Estudiante;
use App\Models\Grupo;
use App\Models\Tutor;

class DashboardController extends Controller
{
    public function index()
    {
        return view('control_escolar.dashboard', [
            'totalEstudiantes' => Estudiante::count(),
            'estudiantesSinGrupo' => Estudiante::whereNull('grupo_id')->count(),
            'estudiantesActivos' => Estudiante::where('estado', 'activo')->count(),
            'totalGrupos' => Grupo::count(),
            'totalCarreras' => Carrera::count(),
            'totalTutores' => Tutor::count(),
            'ciclosActivos' => CicloEscolar::where('estado', 'activo')->count(),
            'tutoresSinGrupo' => Tutor::doesntHave('gruposAsignados')->count(),
        ]);
    }
}
