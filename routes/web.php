<?php

use App\Http\Controllers\Admin\CarreraController;
use App\Http\Controllers\Admin\ExpedientePendienteController;
use App\Http\Controllers\Admin\GrupoController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PersonaController;
use App\Http\Controllers\Admin\PsicologoController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TutorController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Estudiante\DiarioController;
use App\Http\Controllers\Estudiante\EvaluacionController;
use App\Http\Controllers\Estudiante\StudentDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Psicologo\AlertaController as PsicologoAlertaController;
use App\Http\Controllers\Psicologo\DashboardController as PsicologoDashboardController;
use App\Http\Controllers\Psicologo\DiagnosticoController as PsicologoDiagnosticoController;
use App\Http\Controllers\Tutor\DashboardController as TutorDashboardController;
use App\Http\Controllers\Tutor\EstudianteController as TutorEstudianteController;
use App\Http\Controllers\Tutor\GrupoController as TutorGrupoController;
use App\Models\Persona;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
        $request->validate(['acepta' => ['required', 'accepted']]);
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
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->hasRole('admin')) return redirect()->route('admin.dashboard');
        if ($user->hasRole('psicologo')) return redirect()->route('psicologo.dashboard');
        if ($user->hasRole('tutor')) return redirect()->route('tutor.dashboard');
        if ($user->hasRole('estudiante')) return redirect()->route('estudiante.dashboard');
        abort(403, 'No tienes un rol asignado.');
    })->name('dashboard');

    // RUTAS DEL PERFIL
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('perfil.index');
    Route::patch('/perfil', [ProfileController::class, 'update'])->name('perfil.update');
    Route::put('/perfil/persona', [ProfileController::class, 'updatePersona'])->name('perfil.persona.update');
    Route::put('/perfil/password', [ProfileController::class, 'updatePassword'])->name('perfil.password.update');
    Route::put('/perfil/apariencia', [ProfileController::class, 'updateAppearance'])->name('perfil.appearance.update');
    Route::delete('/perfil', [ProfileController::class, 'destroy'])->name('perfil.destroy');

    // Alias Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::put('/profile/appearance', [ProfileController::class, 'updateAppearance'])->name('profile.appearance.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // MÓDULO ESTUDIANTE
    Route::prefix('estudiante')->name('estudiante.')->middleware('role:estudiante')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    });

    Route::prefix('evaluaciones')->name('evaluaciones.')->middleware(['role:estudiante', 'permission:evaluaciones.realizar'])->group(function () {
        Route::get('/', [EvaluacionController::class, 'index'])->name('index');
        Route::get('/{tipo}/aplicar', [EvaluacionController::class, 'aplicar'])->name('aplicar');
        Route::post('/{tipo}/responder', [EvaluacionController::class, 'responder'])->name('responder');
    });

    Route::prefix('diario')->name('diario.')->middleware('role:estudiante')->group(function () {
        Route::get('/', [DiarioController::class, 'index'])->middleware('permission:diario_ia.ver.propio')->name('index');
        Route::post('/', [DiarioController::class, 'store'])->middleware('permission:diario_ia.crear')->name('store');
    });

    // MÓDULO TUTOR
    Route::prefix('tutor')->name('tutor.')->middleware('role:tutor')->group(function () {
        Route::get('/dashboard', [TutorDashboardController::class, 'index'])->name('dashboard');

        Route::prefix('grupos')->name('grupos.')->middleware('permission:grupos.ver.asignados')->group(function () {
            Route::get('/', [TutorGrupoController::class, 'index'])->name('index');
            Route::get('/{grupo}', [TutorGrupoController::class, 'show'])->name('show');
            Route::get('/{grupo}/estudiantes/create', [TutorEstudianteController::class, 'create'])->name('estudiantes.create');
            Route::post('/{grupo}/estudiantes', [TutorEstudianteController::class, 'store'])->name('estudiantes.store');
        });

        Route::get('/estudiantes/{estudiante}/edit', [TutorEstudianteController::class, 'edit'])->name('estudiantes.edit');
        Route::put('/estudiantes/{estudiante}', [TutorEstudianteController::class, 'update'])->name('estudiantes.update');
    });

    // MÓDULO PSICÓLOGO
    Route::prefix('psicologo')->name('psicologo.')->middleware('role:psicologo')->group(function () {
        Route::get('/dashboard', [PsicologoDashboardController::class, 'index'])->name('dashboard');
    });

    Route::prefix('alertas')->name('alertas.')->middleware(['role:psicologo', 'permission:alertas.ver.clinicas'])->group(function () {
        Route::get('/', [PsicologoAlertaController::class, 'index'])->name('index');
        Route::get('/{alerta}', [PsicologoAlertaController::class, 'show'])->name('show');
    });

    Route::prefix('diagnosticos')->name('diagnosticos.')->middleware('role:psicologo')->group(function () {
        Route::get('/', [PsicologoDashboardController::class, 'index'])->middleware('permission:diagnosticos.ver')->name('index');
        Route::post('/', [PsicologoDiagnosticoController::class, 'store'])->middleware('permission:diagnosticos.crear')->name('store');
    });

    Route::prefix('analisis')->name('analisis.')->middleware(['role:psicologo', 'permission:resultados_ia.ver'])->group(function () {
        Route::get('/', [PsicologoDashboardController::class, 'index'])->name('index');
    });

    // MÓDULO ADMINISTRADOR
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {

        Route::prefix('expedientes-pendientes')->name('expedientes-pendientes.')->group(function () {
            Route::get('/', [ExpedientePendienteController::class, 'index'])->name('index');
            Route::get('/{user}/edit', [ExpedientePendienteController::class, 'edit'])->name('edit');
            Route::put('/{user}', [ExpedientePendienteController::class, 'update'])->name('update');
        });

        Route::get('/dashboard', function () {
            return view('admin.dashboard', [
                'totalUsuarios' => User::count(),
                'totalPersonas' => Persona::count(),
                'totalRoles' => Role::count(),
                'totalPermisos' => Permission::count(),
                'usuariosSinPersona' => User::doesntHave('persona')->count(),
                'rolesSinPermisos' => Role::doesntHave('permissions')->count(),
                'estudiantesCount' => User::role('estudiante')->count(),
                'psicologosCount' => User::role('psicologo')->count(),
                'tutoresCount' => Tutor::count(),
                'tutoresSinGrupos' => Tutor::doesntHave('grupos')->count(),
                'estudiantesSinExpediente' => User::role('estudiante')->whereDoesntHave('persona.estudiante')->count()
            ]);
        })->middleware('permission:usuarios.ver')->name('dashboard');

        Route::resource('usuarios', UserController::class)->parameters(['usuarios' => 'user'])->names('usuarios')->except(['show']);

        // AQUÍ ESTÁ LA CORRECCIÓN: Se quitó el ->except(['show'])
        Route::resource('grupos', GrupoController::class)->parameters(['grupos' => 'grupo'])->names('grupos');

        Route::resource('carreras', CarreraController::class)->parameters(['carreras' => 'carrera'])->names('carreras')->except(['show']);
        Route::resource('tutores', TutorController::class)->parameters(['tutores' => 'tutor'])->names('tutores');
        Route::resource('psicologos', PsicologoController::class)->parameters(['psicologos' => 'psicologo'])->names('psicologos');
        Route::resource('personas', PersonaController::class)->parameters(['personas' => 'persona'])->names('personas')->except(['show']);
        Route::resource('roles', RoleController::class)->parameters(['roles' => 'role'])->names('roles');
        Route::resource('permisos', PermissionController::class)->parameters(['permisos' => 'permiso'])->names('permisos');
    });
});

require __DIR__.'/auth.php';
