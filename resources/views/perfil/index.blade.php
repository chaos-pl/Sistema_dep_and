@extends('layouts.app')

@section('title', 'Mi perfil - PROMETEO')
@section('page-title', 'Configuración de Cuenta')
@section('page-subtitle', 'Administra tu información personal y credenciales')

@push('styles')
    <style>
        /* Estilos para seleccionar el avatar en el formulario de apariencia */
        .appearance-icon-input:checked + .appearance-icon-option {
            border-color: var(--app-primary) !important;
            background-color: var(--app-primary-soft) !important;
            color: var(--app-primary-dark) !important;
            transform: scale(1.02);
        }
        .appearance-icon-option {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .appearance-icon-option:hover {
            border-color: #cbd5e1 !important;
            background-color: #f8fafc;
        }
    </style>
@endpush

@section('content')
    @php
        // Nos aseguramos de cargar la relación de persona
        $user = auth()->user()->loadMissing('persona');
    @endphp

    <div class="row g-4">
        <div class="col-12">
            <div class="app-card p-4 p-md-5 bg-primary bg-gradient text-white border-0 shadow-sm rounded-4 position-relative overflow-hidden">
                <div class="position-relative z-1 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
                    <div class="d-flex align-items-center gap-4">
                        <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 80px; height: 80px;">
                            <i class="{{ $user->avatar_icon_class ?? 'bi bi-person-circle' }} fs-1"></i>
                        </div>
                        <div>
                            <h2 class="fw-black mb-1 text-white">{{ $user->name }}</h2>
                            <p class="text-white text-opacity-75 mb-0">{{ $user->email }}</p>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-md-row gap-2">
                        <span class="badge bg-white text-primary rounded-pill px-3 py-2 shadow-sm fs-6">
                            <i class="bi bi-shield-check me-1"></i> {{ $user->getRoleNames()->map(fn($role) => ucfirst($role))->implode(', ') ?: 'Sin rol' }}
                        </span>
                        @if($user->acepto_consentimiento)
                            <span class="badge bg-success border border-white border-opacity-25 rounded-pill px-3 py-2 shadow-sm fs-6">
                                <i class="bi bi-check-circle-fill me-1"></i> Consentimiento al día
                            </span>
                        @else
                            <span class="badge bg-warning text-dark border border-white border-opacity-25 rounded-pill px-3 py-2 shadow-sm fs-6">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Consentimiento pendiente
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="app-card p-4 h-100 rounded-4 border-0 shadow-sm">
                <h5 class="fw-bold mb-4 text-dark border-bottom pb-3"><i class="bi bi-journal-richtext text-primary me-2"></i>Resumen de tu cuenta</h5>

                <div class="d-flex flex-column gap-4">
                    <div>
                        <small class="text-muted fw-bold text-uppercase" style="letter-spacing: 0.5px;">Nombre de Usuario</small>
                        <div class="fw-semibold text-dark fs-5 mt-1">{{ $user->name }}</div>
                    </div>
                    <div>
                        <small class="text-muted fw-bold text-uppercase" style="letter-spacing: 0.5px;">Correo de Acceso</small>
                        <div class="fw-semibold text-dark fs-5 mt-1">{{ $user->email }}</div>
                    </div>

                    @if($user->persona)
                        <div>
                            <small class="text-muted fw-bold text-uppercase" style="letter-spacing: 0.5px;">Nombre Real</small>
                            <div class="fw-semibold text-dark fs-5 mt-1">{{ $user->persona->nombre }} {{ $user->persona->apellido_paterno }}</div>
                        </div>
                    @endif

                    <div class="mt-2 p-3 bg-light rounded-4 border border-secondary border-opacity-10">
                        <small class="text-muted d-block mb-1"><i class="bi bi-calendar-check me-1"></i> Miembro desde</small>
                        <div class="fw-bold text-dark">{{ $user->created_at?->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="d-grid gap-4">
                @include('perfil.partials.information-form')
                @include('perfil.partials.persona-form') @include('perfil.partials.appearance-form')
                @include('perfil.partials.password-form')
                @include('perfil.partials.delete-account-form')
            </div>
        </div>
    </div>
@endsection
