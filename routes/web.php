<?php

use App\Models\User;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PersonaController;
use App\Http\Controllers\Admin\RoleController;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
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

    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
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

    Route::get('/perfil', [ProfileController::class, 'edit'])->name('perfil.index');
    Route::patch('/perfil', [ProfileController::class, 'update'])->name('perfil.update');
    Route::delete('/perfil', [ProfileController::class, 'destroy'])->name('perfil.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('estudiante')
        ->name('estudiante.')
        ->middleware('role:estudiante')
        ->group(function () {
            Route::get('/dashboard', function () {
                return view('estudiante.dashboard');
            })->name('dashboard');
        });

    Route::prefix('evaluaciones')
        ->name('evaluaciones.')
        ->middleware(['role:estudiante', 'permission:evaluaciones.realizar'])
        ->group(function () {
            Route::get('/', function () {
                return view('evaluaciones.index');
            })->name('index');

            Route::get('/{tipo}/aplicar', function ($tipo) {
                return view('evaluaciones.aplicar', compact('tipo'));
            })->name('aplicar');

            Route::post('/{tipo}/responder', function ($tipo) {
                Alert::success('Evaluación enviada', 'Tu respuesta fue registrada correctamente.');
                return redirect()->route('evaluaciones.aplicar', $tipo);
            })->name('responder');
        });

    Route::prefix('diario')
        ->name('diario.')
        ->middleware('role:estudiante')
        ->group(function () {
            Route::get('/', function () {
                return view('diario.index');
            })->middleware('permission:diario_ia.ver.propio')->name('index');

            Route::post('/', function () {
                Alert::success('Entrada guardada', 'Tu texto fue registrado correctamente.');
                return redirect()->route('diario.index');
            })->middleware('permission:diario_ia.crear')->name('store');
        });

    Route::prefix('tutor')
        ->name('tutor.')
        ->middleware('role:tutor')
        ->group(function () {
            Route::get('/dashboard', function () {
                $totalGrupos = 0;
                $completadas = 0;
                $abandonadas = 0;
                $grupos = collect();

                return view('tutor.dashboard', compact('totalGrupos', 'completadas', 'abandonadas', 'grupos'));
            })->name('dashboard');
        });

    Route::prefix('grupos')
        ->name('grupos.')
        ->middleware(['role:tutor', 'permission:grupos.ver.asignados'])
        ->group(function () {
            Route::get('/', function () {
                $grupos = collect();

                return view('tutor.dashboard', [
                    'totalGrupos' => 0,
                    'completadas' => 0,
                    'abandonadas' => 0,
                    'grupos' => $grupos,
                ]);
            })->name('index');

            Route::get('/{id}', function ($id) {
                return redirect()->route('grupos.index');
            })->name('show');
        });

    Route::prefix('psicologo')
        ->name('psicologo.')
        ->middleware('role:psicologo')
        ->group(function () {
            Route::get('/dashboard', function () {
                $alertas = collect();
                $resultados = collect();

                return view('psicologo.dashboard', compact('alertas', 'resultados'));
            })->name('dashboard');
        });

    Route::prefix('alertas')
        ->name('alertas.')
        ->middleware(['role:psicologo', 'permission:alertas.ver.clinicas'])
        ->group(function () {
            Route::get('/', function () {
                $alertas = collect();
                $resultados = collect();

                return view('psicologo.dashboard', compact('alertas', 'resultados'));
            })->name('index');

            Route::get('/{id}', function ($id) {
                return redirect()->route('alertas.index');
            })->name('show');
        });

    Route::prefix('diagnosticos')
        ->name('diagnosticos.')
        ->middleware('role:psicologo')
        ->group(function () {
            Route::get('/', function () {
                $alertas = collect();
                $resultados = collect();

                return view('psicologo.dashboard', compact('alertas', 'resultados'));
            })->middleware('permission:diagnosticos.ver')->name('index');

            Route::post('/', function () {
                Alert::success('Diagnóstico guardado', 'El diagnóstico fue registrado correctamente.');
                return redirect()->route('diagnosticos.index');
            })->middleware('permission:diagnosticos.crear')->name('store');
        });

    Route::prefix('analisis')
        ->name('analisis.')
        ->middleware(['role:psicologo', 'permission:resultados_ia.ver'])
        ->group(function () {
            Route::get('/', function () {
                $alertas = collect();
                $resultados = collect();

                return view('psicologo.dashboard', compact('alertas', 'resultados'));
            })->name('index');
        });

    Route::prefix('admin')
        ->name('admin.')
        ->middleware('role:admin')
        ->group(function () {
            Route::get('/dashboard', function () {
                $totalUsuarios = User::count();
                $totalPersonas = Persona::count();
                $totalRoles = Role::count();
                $totalPermisos = Permission::count();

                return view('admin.dashboard', compact(
                    'totalUsuarios',
                    'totalPersonas',
                    'totalRoles',
                    'totalPermisos'
                ));
            })->middleware('permission:usuarios.ver')->name('dashboard');

            Route::resource('usuarios', UserController::class)
                ->parameters(['usuarios' => 'user'])
                ->names('usuarios')
                ->except(['show']);

            Route::resource('personas', PersonaController::class)
                ->parameters(['personas' => 'persona'])
                ->names('personas')
                ->except(['show']);

            Route::get('/roles', [RoleController::class, 'index'])
                ->middleware('permission:roles.ver')
                ->name('roles.index');

            Route::get('/roles/create', [RoleController::class, 'create'])
                ->middleware('permission:roles.crear')
                ->name('roles.create');

            Route::post('/roles', [RoleController::class, 'store'])
                ->middleware('permission:roles.crear')
                ->name('roles.store');

            Route::get('/roles/{role}', [RoleController::class, 'show'])
                ->middleware('permission:roles.ver')
                ->name('roles.show');

            Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
                ->middleware('permission:roles.editar')
                ->name('roles.edit');

            Route::put('/roles/{role}', [RoleController::class, 'update'])
                ->middleware('permission:roles.editar')
                ->name('roles.update');

            Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
                ->middleware('permission:roles.eliminar')
                ->name('roles.destroy');

            Route::get('/permisos', function () {
                $permisos = Permission::orderBy('name')->get();
                return view('admin.permisos.index', compact('permisos'));
            })->middleware('permission:permisos.ver')->name('permisos.index');
        });
});

require __DIR__.'/auth.php';
