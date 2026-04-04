@extends('layouts.app')

@section('title', 'Estado pendiente - PROMETEO')
@section('page-title', 'Inicio del estudiante')
@section('page-subtitle', 'Estado de tu expediente académico')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="app-card p-5 border-0 shadow-sm rounded-4 text-center">
                <div class="mb-4">
                    <div class="mx-auto d-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-10 text-warning"
                         style="width: 90px; height: 90px;">
                        <i class="bi bi-hourglass-split fs-1"></i>
                    </div>
                </div>

                <h2 class="fw-black mb-3">{{ $titulo }}</h2>
                <p class="text-muted fs-5 mb-4">
                    {{ $mensaje }}
                </p>

                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                        <i class="bi bi-info-circle me-1"></i>
                        Estado: pendiente
                    </span>

                    <span class="badge bg-light text-dark border rounded-pill px-3 py-2">
                        <i class="bi bi-person-badge me-1"></i>
                        Rol: estudiante
                    </span>
                </div>

                <div class="mt-4">
                    <p class="text-muted small mb-0">
                        Cuando administración o control escolar complete tu expediente, aquí aparecerá tu panel normal.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
