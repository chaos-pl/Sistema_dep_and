<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use App\Models\Tutor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $metricas = $this->obtenerMetricas();

        return view('admin.dashboard', array_merge([
            'metricas' => $metricas,
        ], $this->variablesCompatibles($metricas)));
    }

    public function metricasTiempoReal()
    {
        return response()->json($this->obtenerMetricas());
    }

    private function obtenerMetricas(): array
    {
        $totalUsuarios = User::count();
        $totalPersonas = Persona::count();
        $totalRoles = Role::count();
        $totalPermisos = Permission::count();

        $totalEstudiantes = User::role('estudiante')->count();
        $totalPsicologos = User::role('psicologo')->count();
        $totalTutores = Tutor::count();

        $totalGrupos = $this->contarTabla('grupos');
        $totalCarreras = $this->contarTabla('carreras');

        $usuariosSinPersona = User::doesntHave('persona')->count();

        $rolesSinPermisos = Role::doesntHave('permissions')->count();

        $tutoresSinGrupos = Tutor::doesntHave('grupos')->count();

        $estudiantesSinExpediente = User::role('estudiante')
            ->whereDoesntHave('persona.estudiante')
            ->count();

        $phq9 = $this->contarEvaluacionesPorTipo(['PHQ-9', 'PHQ9', 'phq9', 'phq-9']);
        $gad7 = $this->contarEvaluacionesPorTipo(['GAD-7', 'GAD7', 'gad7', 'gad-7']);

        $diarios = $this->contarTabla('analisis_nlps');

        $alertasPendientes = $this->contarAlertasPorEstado([
            'pendiente',
            'Pendiente',
            'PENDIENTE',
        ]);

        $alertasActivas = $this->contarAlertasPorEstado([
            'activa',
            'activo',
            'Activa',
            'Activo',
            'ACTIVA',
            'ACTIVO',
        ]);

        $alertasAtendidas = $this->contarAlertasPorEstado([
            'atendida',
            'atendido',
            'cerrada',
            'cerrado',
            'Atendida',
            'Atendido',
            'Cerrada',
            'Cerrado',
            'ATENDIDA',
            'ATENDIDO',
            'CERRADA',
            'CERRADO',
        ]);

        $casosPrioritarios = $this->contarCasosPrioritarios();

        return [
            'resumen' => [
                'totalUsuarios' => $totalUsuarios,
                'totalPersonas' => $totalPersonas,
                'totalRoles' => $totalRoles,
                'totalPermisos' => $totalPermisos,
                'totalEstudiantes' => $totalEstudiantes,
                'totalPsicologos' => $totalPsicologos,
                'totalTutores' => $totalTutores,
                'totalGrupos' => $totalGrupos,
                'totalCarreras' => $totalCarreras,
            ],

            'evaluaciones' => [
                'phq9' => $phq9,
                'gad7' => $gad7,
                'diarios' => $diarios,
            ],

            'alertas' => [
                'pendientes' => $alertasPendientes,
                'activas' => $alertasActivas,
                'atendidas' => $alertasAtendidas,
                'prioritarias' => $casosPrioritarios,
            ],

            'salud' => [
                'usuariosSinPersona' => $usuariosSinPersona,
                'estudiantesSinExpediente' => $estudiantesSinExpediente,
                'rolesSinPermisos' => $rolesSinPermisos,
                'tutoresSinGrupos' => $tutoresSinGrupos,
            ],

            'actividadMensual' => $this->actividadMensual(),
        ];
    }

    private function variablesCompatibles(array $metricas): array
    {
        return [
            'totalUsuarios' => $metricas['resumen']['totalUsuarios'],
            'totalPersonas' => $metricas['resumen']['totalPersonas'],
            'totalRoles' => $metricas['resumen']['totalRoles'],
            'totalPermisos' => $metricas['resumen']['totalPermisos'],

            'usuariosSinPersona' => $metricas['salud']['usuariosSinPersona'],
            'rolesSinPermisos' => $metricas['salud']['rolesSinPermisos'],
            'estudiantesSinExpediente' => $metricas['salud']['estudiantesSinExpediente'],
            'tutoresSinGrupos' => $metricas['salud']['tutoresSinGrupos'],

            'estudiantesCount' => $metricas['resumen']['totalEstudiantes'],
            'psicologosCount' => $metricas['resumen']['totalPsicologos'],
            'tutoresCount' => $metricas['resumen']['totalTutores'],
        ];
    }

    private function contarTabla(string $tabla): int
    {
        if (! Schema::hasTable($tabla)) {
            return 0;
        }

        return DB::table($tabla)->count();
    }

    private function contarEvaluacionesPorTipo(array $tipos): int
    {
        $tablasPosibles = [
            'evaluaciones',
            'resultados_evaluaciones',
            'respuestas_evaluaciones',
            'evaluacion_resultados',
        ];

        foreach ($tablasPosibles as $tabla) {
            if (! Schema::hasTable($tabla)) {
                continue;
            }

            $columnasPosibles = [
                'tipo',
                'instrumento',
                'nombre',
                'test',
                'escala',
                'cuestionario',
            ];

            foreach ($columnasPosibles as $columna) {
                if (Schema::hasColumn($tabla, $columna)) {
                    return DB::table($tabla)
                        ->whereIn($columna, $tipos)
                        ->count();
                }
            }
        }

        return 0;
    }

    private function contarAlertasPorEstado(array $estados): int
    {
        if (! Schema::hasTable('alertas')) {
            return 0;
        }

        if (! Schema::hasColumn('alertas', 'estado')) {
            return 0;
        }

        return DB::table('alertas')
            ->whereIn('estado', $estados)
            ->count();
    }

    private function contarCasosPrioritarios(): int
    {
        if (! Schema::hasTable('alertas')) {
            return 0;
        }

        if (Schema::hasColumn('alertas', 'prioridad')) {
            return DB::table('alertas')
                ->whereIn('prioridad', [
                    'alta',
                    'critica',
                    'crítica',
                    'urgente',
                    'Alta',
                    'Critica',
                    'Crítica',
                    'Urgente',
                    'ALTA',
                    'CRITICA',
                    'CRÍTICA',
                    'URGENTE',
                ])
                ->count();
        }

        if (Schema::hasColumn('alertas', 'nivel_riesgo')) {
            return DB::table('alertas')
                ->whereIn('nivel_riesgo', [
                    'alto',
                    'severo',
                    'critico',
                    'crítico',
                    'Alto',
                    'Severo',
                    'Critico',
                    'Crítico',
                    'ALTO',
                    'SEVERO',
                    'CRITICO',
                    'CRÍTICO',
                ])
                ->count();
        }

        return 0;
    }

    private function actividadMensual(): array
    {
        $labels = [];
        $usuarios = [];
        $evaluaciones = [];
        $alertas = [];

        for ($i = 5; $i >= 0; $i--) {
            $fecha = now()->subMonths($i);

            $inicio = $fecha->copy()->startOfMonth();
            $fin = $fecha->copy()->endOfMonth();

            $labels[] = $this->nombreMesCorto($fecha);

            $usuarios[] = $this->contarPorMes('users', $inicio, $fin);
            $evaluaciones[] = $this->contarEvaluacionesPorMes($inicio, $fin);
            $alertas[] = $this->contarPorMes('alertas', $inicio, $fin);
        }

        return [
            'labels' => $labels,
            'usuarios' => $usuarios,
            'evaluaciones' => $evaluaciones,
            'alertas' => $alertas,
        ];
    }

    private function contarPorMes(string $tabla, Carbon $inicio, Carbon $fin): int
    {
        if (! Schema::hasTable($tabla)) {
            return 0;
        }

        if (! Schema::hasColumn($tabla, 'created_at')) {
            return 0;
        }

        return DB::table($tabla)
            ->whereBetween('created_at', [$inicio, $fin])
            ->count();
    }

    private function contarEvaluacionesPorMes(Carbon $inicio, Carbon $fin): int
    {
        $tablasPosibles = [
            'evaluaciones',
            'resultados_evaluaciones',
            'respuestas_evaluaciones',
            'evaluacion_resultados',
        ];

        foreach ($tablasPosibles as $tabla) {
            if (! Schema::hasTable($tabla)) {
                continue;
            }

            if (! Schema::hasColumn($tabla, 'created_at')) {
                continue;
            }

            return DB::table($tabla)
                ->whereBetween('created_at', [$inicio, $fin])
                ->count();
        }

        return 0;
    }

    private function nombreMesCorto(Carbon $fecha): string
    {
        $meses = [
            1 => 'Ene',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Abr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Ago',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dic',
        ];

        return $meses[(int) $fecha->format('n')];
    }
}
