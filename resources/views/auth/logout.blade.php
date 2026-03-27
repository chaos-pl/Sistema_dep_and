@extends('layouts.app')

@section('title', 'Cerrar sesión - PROMETEO')
@section('page-title', 'Cerrar sesión')
@section('page-subtitle', 'Confirma que deseas salir del sistema')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="app-card p-4 p-md-5 text-center">
                <div class="mb-4">
                    <div class="metric-icon mx-auto" style="width:72px;height:72px;">
                        <i class="bi bi-box-arrow-right fs-2"></i>
                    </div>
                </div>

                <h2 class="fw-bold mb-3">¿Deseas cerrar sesión?</h2>
                <p class="text-muted mb-4">
                    Estás a punto de salir de <strong>PROMETEO</strong>. Asegúrate de haber guardado tus cambios antes de continuar.
                </p>

                <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                    <a href="{{ route('dashboard') }}" class="btn btn-light border px-4">
                        <i class="bi bi-arrow-left me-2"></i>Volver
                    </a>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-box-arrow-right me-2"></i>Sí, cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
