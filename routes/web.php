<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/consentimiento', fn () => view('consentimiento.create'))->name('consentimiento.create');
    Route::post('/consentimiento', fn () => redirect()->route('dashboard')->with('status', 'Consentimiento registrado correctamente.'))->name('consentimiento.store');

    Route::get('/estudiante/dashboard', fn () => view('dashboard'))->name('estudiante.dashboard');
    Route::get('/estudiante/tamizaje', fn () => view('estudiante.tamizaje'))->name('tamizaje.show');
    Route::get('/estudiante/diario', fn () => view('estudiante.dashboard'))->name('diario.index');

    Route::get('/tutor/dashboard', fn () => view('dashboard'))->name('tutor.dashboard');
    Route::get('/tutor/grupos', fn () => view('tutor.dashboard'))->name('grupos.index');
    Route::get('/tutor/reportes', fn () => view('tutor.dashboard'))->name('reportes.tutor');
    Route::get('/tutor/seguimiento', fn () => view('tutor.dashboard'))->name('seguimiento.index');

    Route::get('/psicologo/dashboard', fn () => view('dashboard'))->name('psicologo.dashboard');
    Route::get('/psicologo/alertas', fn () => view('psicologo.dashboard'))->name('alertas.index');
    Route::get('/psicologo/diagnosticos', fn () => view('psicologo.dashboard'))->name('diagnosticos.index');
    Route::get('/psicologo/analisis', fn () => view('psicologo.dashboard'))->name('analisis.index');

    Route::get('/aviso-de-privacidad', fn () => view('consentimiento.create'))->name('aviso.privacidad');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
