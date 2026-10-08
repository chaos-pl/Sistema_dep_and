<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CarreraController;
use App\Http\Controllers\Admin\EstudianteController as AdminEstudianteController;
use App\Http\Controllers\Admin\ExpedientePendienteController;
use App\Http\Controllers\Admin\GrupoController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PersonaController;
use App\Http\Controllers\Admin\PsicologoController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TutorController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UsuarioSinPersonaController;
use App\Http\Controllers\AvisoController;
use App\Http\Controllers\ControlEscolar\AsignacionController as CEAsignacionController;
use App\Http\Controllers\ControlEscolar\CarreraController as CECarreraController;
use App\Http\Controllers\ControlEscolar\CicloEscolarController as CECicloEscolarController;
use App\Http\Controllers\ControlEscolar\DashboardController as CEDashboardController;
use App\Http\Controllers\ControlEscolar\EstudianteController as CEEstudianteController;
use App\Http\Controllers\ControlEscolar\GrupoController as CEGrupoController;
use App\Http\Controllers\ControlEscolar\TutorController as CETutorController;
use App\Http\Controllers\Dass21Controller;
use App\Http\Controllers\Estudiante\DiarioController;
use App\Http\Controllers\Estudiante\EvaluacionController;
use App\Http\Controllers\Estudiante\StudentDashboardController;
use App\Http\Controllers\EvolucionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilePhotoController;
use App\Http\Controllers\Psicologo\AlertaController as PsicologoAlertaController;
use App\Http\Controllers\Psicologo\AnalisisNlpController as PsicologoAnalisisNlpController;
use App\Http\Controllers\Psicologo\CasoAtencionController;
use App\Http\Controllers\Psicologo\DashboardController as PsicologoDashboardController;
use App\Http\Controllers\Psicologo\DiagnosticoController as PsicologoDiagnosticoController;
use App\Http\Controllers\Psicologo\TamizajeController;
use App\Http\Controllers\QuestionnaireDraftController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\Tutor\DashboardController as TutorDashboardController;
use App\Http\Controllers\Tutor\EstudianteController as TutorEstudianteController;
use App\Http\Controllers\Tutor\GrupoController as TutorGrupoController;
use App\Models\Tutor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use RealRashid\SweetAlert\Facades\Alert;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::view('/aviso-privacidad', 'aviso.privacidad')->name('aviso.privacidad');

Route::middleware(['auth', 'no.cache'])->group(function () {
    Route::get('/consentimiento', function () {
        if (auth()->user()->acepto_consentimiento) {
            return redirect()->route('dashboard');
        }

        return view('consentimiento.create');
    })->name('consentimiento.create');

    Route::post('/consentimiento', function (Request $request) {
        $request->validate([
            'acepta' => ['required', 'accepted'],
        ]);

        auth()->user()->update([
            'acepto_consentimiento' => true,
            'consentimiento_aceptado_at' => now(),
        ]);

        Alert::success('Consentimiento aceptado', 'Ya puedes ingresar a PROMETEO.');

        return redirect()->route('dashboard');
    })->name('consentimiento.store');

    Route::post('/consentimiento/rechazar', function () {
        auth()->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        Alert::warning('Acceso cancelado', 'Debes aceptar el consentimiento para usar el sistema.');

        return redirect()->route('login');
    })->name('consentimiento.rechazar');

    Route::get('/cerrar-sesion', function () {
        return view('auth.logout');
    })->name('logout.view');
});

Route::middleware(['auth', 'consent.accepted', 'no.cache'])->group(function () {
    Route::put('/cuestionarios/{instrument}/borrador', [QuestionnaireDraftController::class, 'store'])
        ->middleware(['role:estudiante', 'permission:evaluaciones.realizar'])->name('questionnaires.draft');
    Route::get('/evolucion', [EvolucionController::class, 'own'])->name('evolucion.own');
    Route::get('/psicologo/estudiantes/{estudiante}/evolucion', [EvolucionController::class, 'show'])->name('evolucion.show');
    Route::get('/notificaciones', [AvisoController::class, 'index'])->name('avisos.index');
    Route::patch('/notificaciones/{aviso}', [AvisoController::class, 'update'])->name('avisos.update');
    Route::post('/notificaciones/{aviso}/abrir', [AvisoController::class, 'open'])->name('avisos.open');
    Route::get('/retroalimentacion/{evaluacion}', [AvisoController::class, 'feedback'])->name('retroalimentacion.show');
    Route::get('/reportes/participacion', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/participacion/exportar', [ReporteController::class, 'export'])->name('reportes.export');

    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('control_escolar')) {
            return redirect()->route('control_escolar.dashboard');
        }

        if ($user->hasRole('psicologo')) {
            return redirect()->route('psicologo.dashboard');
        }

        if ($user->hasRole('tutor')) {
            return redirect()->route('tutor.dashboard');
        }

        if ($user->hasRole('estudiante')) {
            return redirect()->route('estudiante.dashboard');
        }

        abort(403, 'No tienes un rol asignado.');
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | PERFIL
    |--------------------------------------------------------------------------
    */

    Route::get('/perfil/foto', [ProfilePhotoController::class, 'show'])->name('perfil.photo.show');
    Route::post('/perfil/foto', [ProfilePhotoController::class, 'store'])->name('perfil.photo.store');
    Route::delete('/perfil/foto', [ProfilePhotoController::class, 'destroy'])->name('perfil.photo.destroy');
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('perfil.index');
    Route::patch('/perfil', [ProfileController::class, 'update'])->name('perfil.update');
    Route::put('/perfil/persona', [ProfileController::class, 'updatePersona'])->name('perfil.persona.update');
    Route::put('/perfil/password', [ProfileController::class, 'updatePassword'])->name('perfil.password.update');
    Route::put('/perfil/apariencia', [ProfileController::class, 'updateAppearance'])->name('perfil.appearance.update');
    Route::delete('/perfil', [ProfileController::class, 'destroy'])->name('perfil.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::put('/profile/appearance', [ProfileController::class, 'updateAppearance'])->name('profile.appearance.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | MÓDULO ESTUDIANTE
    |--------------------------------------------------------------------------
    */

    Route::prefix('estudiante')
        ->name('estudiante.')
        ->middleware('role:estudiante')
        ->group(function () {
            Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        });

    Route::prefix('evaluaciones')
        ->name('evaluaciones.')
        ->middleware(['role:estudiante', 'permission:evaluaciones.realizar'])
        ->group(function () {
            Route::get('/', [EvaluacionController::class, 'index'])->name('index');
            Route::get('/{tipo}/aplicar', [EvaluacionController::class, 'aplicar'])->name('aplicar');
            Route::post('/{tipo}/responder', [EvaluacionController::class, 'responder'])->name('responder');
        });

    Route::prefix('dass21')
        ->name('dass21.')
        ->middleware(['role:estudiante', 'permission:evaluaciones.realizar'])
        ->group(function () {
            Route::get('/historial', [Dass21Controller::class, 'history'])->middleware('permission:evaluaciones.historial.propio')->name('history');
            Route::get('/aplicar', [Dass21Controller::class, 'create'])->name('create');
            Route::post('/aplicar', [Dass21Controller::class, 'store'])->name('store');
            Route::get('/resultados/{evaluation}', [Dass21Controller::class, 'show'])->name('show');
        });

    Route::prefix('diario')
        ->name('diario.')
        ->middleware('role:estudiante')
        ->group(function () {
            Route::get('/', [DiarioController::class, 'index'])
                ->middleware('permission:diario_ia.ver.propio')
                ->name('index');

            Route::post('/', [DiarioController::class, 'store'])
                ->middleware('permission:diario_ia.crear')
                ->name('store');
        });

    /*
    |--------------------------------------------------------------------------
    | MÓDULO TUTOR
    |--------------------------------------------------------------------------
    */

    Route::prefix('tutor')
        ->name('tutor.')
        ->middleware('role:tutor')
        ->group(function () {
            Route::get('/dashboard', [TutorDashboardController::class, 'index'])->name('dashboard');
            Route::get('/seguimiento', [TutorGrupoController::class, 'seguimiento'])
                ->middleware(['permission:alertas.ver.general', 'permission:grupos.ver.asignados', 'permission:usuarios.ver.grupo'])
                ->name('seguimiento');

            Route::prefix('grupos')
                ->name('grupos.')
                ->middleware('permission:grupos.ver.asignados')
                ->group(function () {
                    Route::get('/', [TutorGrupoController::class, 'index'])->name('index');
                    Route::get('/{grupo}', [TutorGrupoController::class, 'show'])->name('show');

                    Route::get('/{grupo}/estudiantes/create', [TutorEstudianteController::class, 'create'])
                        ->name('estudiantes.create');

                    Route::post('/{grupo}/estudiantes', [TutorEstudianteController::class, 'store'])
                        ->name('estudiantes.store');
                });

            Route::get('/estudiantes/{estudiante}/edit', [TutorEstudianteController::class, 'edit'])
                ->name('estudiantes.edit');

            Route::put('/estudiantes/{estudiante}', [TutorEstudianteController::class, 'update'])
                ->name('estudiantes.update');
        });

    /*
    |--------------------------------------------------------------------------
    | MÓDULO PSICÓLOGO
    |--------------------------------------------------------------------------
    */

    Route::prefix('psicologo')
        ->name('psicologo.')
        ->middleware('role:psicologo')
        ->group(function () {
            Route::get('/casos', [CasoAtencionController::class, 'index'])->name('casos.index');
            Route::get('/casos/{caso}', [CasoAtencionController::class, 'show'])->name('casos.show');
            Route::post('/casos/{caso}', [CasoAtencionController::class, 'update'])->middleware('permission:diagnosticos.crear')->name('casos.update');
            Route::get('/tamizajes', [TamizajeController::class, 'index'])->middleware('permission:evaluaciones.historial.global')->name('tamizajes.index');
            Route::get('/tamizajes/{evaluacion}', [TamizajeController::class, 'show'])->middleware(['permission:evaluaciones.historial.global', 'permission:evaluaciones.respuestas.detalle'])->name('tamizajes.show');
            Route::get('/dashboard', [PsicologoDashboardController::class, 'index'])
                ->name('dashboard');
        });

    Route::prefix('alertas')
        ->name('alertas.')
        ->middleware(['role:psicologo', 'permission:alertas.ver.clinicas'])
        ->group(function () {
            Route::get('/', [PsicologoAlertaController::class, 'index'])
                ->name('index');

            Route::get('/{alerta}', [PsicologoAlertaController::class, 'show'])
                ->middleware('permission:evaluaciones.respuestas.detalle')
                ->name('show');
        });

    Route::prefix('diagnosticos')
        ->name('diagnosticos.')
        ->middleware('role:psicologo')
        ->group(function () {
            Route::get('/', [PsicologoDiagnosticoController::class, 'index'])
                ->middleware('permission:diagnosticos.ver')
                ->name('index');

            Route::post('/', [PsicologoDiagnosticoController::class, 'store'])
                ->middleware('permission:diagnosticos.crear')
                ->name('store');
        });

    Route::prefix('analisis')
        ->name('analisis.')
        ->middleware(['role:psicologo', 'permission:resultados_ia.ver'])
        ->group(function () {
            Route::get('/', [PsicologoAnalisisNlpController::class, 'index'])
                ->name('index');

            Route::post('/reanalizar-pendientes', [PsicologoAnalisisNlpController::class, 'reanalizarPendientes'])
                ->name('reanalizar-pendientes');

            Route::post('/{analisisNlp}/reanalizar', [PsicologoAnalisisNlpController::class, 'reanalizar'])
                ->name('reanalizar');

            Route::get('/{analisisNlp}', [PsicologoAnalisisNlpController::class, 'show'])
                ->name('show');
        });
    /*
|--------------------------------------------------------------------------
| MÓDULO ADMINISTRADOR
|--------------------------------------------------------------------------
*/

    // Expedientes compartidos: no ampliar el acceso al resto del módulo admin.
    Route::prefix('admin/expedientes-pendientes')
        ->name('admin.expedientes-pendientes.')
        ->middleware('role:admin|control_escolar')
        ->group(function () {
            Route::get('/', [ExpedientePendienteController::class, 'index'])
                ->middleware('role_or_permission:admin|estudiantes.ver_pendientes')->name('index');
            Route::get('/{user}/edit', [ExpedientePendienteController::class, 'edit'])
                ->middleware('role_or_permission:admin|estudiantes.ver_pendientes')->name('edit');
            Route::put('/{user}', [ExpedientePendienteController::class, 'update'])
                ->middleware('role_or_permission:admin|estudiantes.asignar_grupo')->name('update');
        });

    Route::prefix('admin')
        ->name('admin.')
        ->middleware('role:admin')
        ->group(function () {
            Route::prefix('usuarios-sin-persona')
                ->name('usuarios-sin-persona.')
                ->group(function () {
                    Route::get('/', [UsuarioSinPersonaController::class, 'index'])->name('index');
                    Route::get('/{user}/edit', [UsuarioSinPersonaController::class, 'edit'])->name('edit');
                    Route::put('/{user}', [UsuarioSinPersonaController::class, 'update'])->name('update');
                });

            Route::get('/dashboard', [AdminDashboardController::class, 'index'])
                ->middleware('permission:usuarios.ver')
                ->name('dashboard');

            Route::get('/dashboard/metricas-tiempo-real', [AdminDashboardController::class, 'metricasTiempoReal'])
                ->middleware('permission:usuarios.ver')
                ->name('dashboard.metricas');

            Route::resource('usuarios', UserController::class)
                ->parameters(['usuarios' => 'user'])
                ->names('usuarios')
                ->except(['show']);

            Route::resource('grupos', GrupoController::class)
                ->parameters(['grupos' => 'grupo'])
                ->names('grupos');

            Route::get('estudiantes', [AdminEstudianteController::class, 'index'])
                ->name('estudiantes.index');

            Route::put('estudiantes/{estudiante}/grupo', [AdminEstudianteController::class, 'updateGrupo'])
                ->name('estudiantes.updateGrupo');

            Route::resource('carreras', CarreraController::class)
                ->parameters(['carreras' => 'carrera'])
                ->names('carreras')
                ->except(['show']);

            Route::resource('tutores', TutorController::class)
                ->parameters(['tutores' => 'tutor'])
                ->names('tutores');

            Route::resource('psicologos', PsicologoController::class)
                ->parameters(['psicologos' => 'psicologo'])
                ->names('psicologos');

            Route::resource('personas', PersonaController::class)
                ->parameters(['personas' => 'persona'])
                ->names('personas')
                ->except(['show']);

            Route::resource('roles', RoleController::class)
                ->parameters(['roles' => 'role'])
                ->names('roles');

            Route::resource('permisos', PermissionController::class)
                ->parameters(['permisos' => 'permiso'])
                ->names('permisos');
        });
    /*
    |--------------------------------------------------------------------------
    | MÓDULO CONTROL ESCOLAR
    |--------------------------------------------------------------------------
    */

    Route::prefix('control-escolar')
        ->name('control_escolar.')
        ->middleware('role:admin|control_escolar')
        ->group(function () {
            Route::get('/dashboard', [CEDashboardController::class, 'index'])
                ->name('dashboard');

            /*
            |--------------------------------------------------------------------------
            | Carreras
            |--------------------------------------------------------------------------
            */

            Route::resource('carreras', CECarreraController::class)
                ->parameters(['carreras' => 'carrera'])
                ->except(['create', 'edit', 'show']);

            /*
            |--------------------------------------------------------------------------
            | Ciclos escolares
            |--------------------------------------------------------------------------
            */

            Route::resource('ciclos-escolares', CECicloEscolarController::class)
                ->parameters(['ciclos-escolares' => 'cicloEscolar'])
                ->except(['create', 'edit', 'show']);

            /*
            |--------------------------------------------------------------------------
            | Grupos
            |--------------------------------------------------------------------------
            */

            Route::resource('grupos', CEGrupoController::class)
                ->parameters(['grupos' => 'grupo'])
                ->except(['create', 'edit']);

            /*
            |--------------------------------------------------------------------------
            | Pendientes de asignación
            |--------------------------------------------------------------------------
            */

            Route::get('pendientes-asignacion', [CEEstudianteController::class, 'pendientes'])
                ->name('pendientes.index');

            Route::put('pendientes-asignacion/{estudiante}/asignar-grupo', [CEEstudianteController::class, 'asignarGrupo'])
                ->name('pendientes.asignarGrupo');

            /*
            |--------------------------------------------------------------------------
            | Estudiantes - Control Escolar
            |--------------------------------------------------------------------------
            */

            Route::get('estudiantes', [CEEstudianteController::class, 'index'])
                ->name('estudiantes.index');

            Route::get('estudiantes/create', [CEEstudianteController::class, 'create'])
                ->name('estudiantes.create');

            Route::post('estudiantes', [CEEstudianteController::class, 'store'])
                ->name('estudiantes.store');

            Route::get('estudiantes/{estudiante}/edit', [CEEstudianteController::class, 'edit'])
                ->name('estudiantes.edit');

            Route::put('estudiantes/{estudiante}', [CEEstudianteController::class, 'update'])
                ->name('estudiantes.update');

            Route::delete('estudiantes/{estudiante}', [CEEstudianteController::class, 'destroy'])
                ->name('estudiantes.destroy');

            Route::put('estudiantes/{estudiante}/grupo', [CEEstudianteController::class, 'updateGrupo'])
                ->name('estudiantes.updateGrupo');

            Route::delete('estudiantes/{estudiante}/grupo', [CEEstudianteController::class, 'quitarGrupo'])
                ->name('estudiantes.quitarGrupo');

            Route::get('estudiantes/{estudiante}/historial', [CEEstudianteController::class, 'historial'])
                ->name('estudiantes.historial');

            /*
            |--------------------------------------------------------------------------
            | Tutores - Control Escolar
            |--------------------------------------------------------------------------
            */

            Route::get('tutores', [CETutorController::class, 'index'])
                ->name('tutores.index');

            Route::get('tutores/create', [CETutorController::class, 'create'])
                ->name('tutores.create');

            Route::post('tutores', [CETutorController::class, 'store'])
                ->name('tutores.store');

            Route::get('tutores/{tutor}/edit', [CETutorController::class, 'edit'])
                ->name('tutores.edit');

            Route::put('tutores/{tutor}', [CETutorController::class, 'update'])
                ->name('tutores.update');

            Route::delete('tutores/{tutor}', [CETutorController::class, 'destroy'])
                ->name('tutores.destroy');

            /*
            |--------------------------------------------------------------------------
            | Asignaciones tutor-grupo
            |--------------------------------------------------------------------------
            */

            Route::resource('asignaciones', CEAsignacionController::class)
                ->parameters(['asignaciones' => 'asignacion'])
                ->only(['index', 'store', 'destroy']);
        });
});

require __DIR__.'/auth.php';
