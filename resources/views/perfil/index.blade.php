@extends('layouts.app')

@section('title', 'Mi Perfil - PROMETEO')
@section('page-title', 'Configuración de Cuenta')
@section('page-subtitle', 'Administra tu información personal y credenciales')

@php
    // 1. OBTENEMOS EL COLOR ELEGIDO POR EL USUARIO (Por defecto morado/purple)
    $userAccentColor = auth()->user()->appearance_settings['accent_color'] ?? 'purple';

    // 2. DEFINIMOS LAS PALETAS DE GRANIM COMPLEJAS (3 colores por degradado)
    $granimPalettes = match($userAccentColor) {
        'blue' => "
            [ { color: '#1e3a8a', pos: 0 }, { color: '#2563eb', pos: .5 }, { color: '#93c5fd', pos: 1 } ],
            [ { color: '#2563eb', pos: 0 }, { color: '#0284c7', pos: .5 }, { color: '#38bdf8', pos: 1 } ],
            [ { color: '#0f172a', pos: 0 }, { color: '#1d4ed8', pos: .5 }, { color: '#3b82f6', pos: 1 } ]
        ",
        'green' => "
            [ { color: '#064e3b', pos: 0 }, { color: '#059669', pos: .5 }, { color: '#6ee7b7', pos: 1 } ],
            [ { color: '#059669', pos: 0 }, { color: '#0d9488', pos: .5 }, { color: '#2dd4bf', pos: 1 } ],
            [ { color: '#022c22', pos: 0 }, { color: '#047857', pos: .5 }, { color: '#10b981', pos: 1 } ]
        ",
        'pink' => "
            [ { color: '#831843', pos: 0 }, { color: '#db2777', pos: .5 }, { color: '#f9a8d4', pos: 1 } ],
            [ { color: '#db2777', pos: 0 }, { color: '#e11d48', pos: .5 }, { color: '#f43f5e', pos: 1 } ],
            [ { color: '#4c0519', pos: 0 }, { color: '#be185d', pos: .5 }, { color: '#ec4899', pos: 1 } ]
        ",
        default => "
            [ { color: '#4c1d95', pos: 0 }, { color: '#7c3aed', pos: .5 }, { color: '#a78bfa', pos: 1 } ],
            [ { color: '#7c3aed', pos: 0 }, { color: '#c026d3', pos: .5 }, { color: '#db2777', pos: 1 } ],
            [ { color: '#1e1b4b', pos: 0 }, { color: '#6d28d9', pos: .5 }, { color: '#8b5cf6', pos: 1 } ]
        " // Morado (Purple)
    };
@endphp

@push('styles')
    <style>
        .anime-item { opacity: 0; transform: translateY(20px); }

        .hover-elevate { transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease; }
        .hover-elevate:hover { transform: translateY(-3px); box-shadow: 0 10px 24px rgba(0,0,0,0.08) !important; border-color: var(--app-primary-soft) !important; }

        /* Banner de Perfil */
        .bg-profile-banner {
            position: relative;
            overflow: hidden;
            background-color: var(--app-primary);
            border: 1px solid rgba(255,255,255,0.05);
        }

        .bg-profile-banner::after {
            content: '\F4B3';
            font-family: "bootstrap-icons"; position: absolute;
            top: -20%; right: -5%; font-size: 16rem; color: #ffffff;
            opacity: 0.08; transform: rotate(-15deg); pointer-events: none;
            z-index: 2;
        }

        #granim-canvas-profile {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            border-radius: inherit;
        }

        .profile-banner-content {
            position: relative;
            z-index: 3;
        }

        .profile-avatar-large {
            width: 90px; height: 90px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            flex-shrink: 0;
        }

        .profile-avatar-large i {
            font-size: 3rem;
            color: #ffffff;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
        }

        .appearance-icon-input:checked + .appearance-icon-option {
            border-color: var(--app-primary) !important;
            background-color: var(--app-primary-soft) !important;
            color: var(--app-primary-dark) !important;
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .appearance-icon-option {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            cursor: pointer;
            border: 2px solid transparent;
        }
        .appearance-icon-option:hover {
            border-color: var(--app-primary-soft) !important;
            transform: translateY(-2px);
        }
    </style>
@endpush

@section('content')
    @php
        $user = auth()->user()->loadMissing('persona');
        $rolPrincipal = $user->getRoleNames()->map(fn($role) => ucfirst($role))->first() ?: 'Sin rol asignado';
    @endphp

    <div class="row g-4">
        <div class="col-12 anime-item">
            <div class="app-card bg-profile-banner p-4 p-md-5 rounded-4 border-0 shadow-lg text-white">

                <canvas id="granim-canvas-profile"></canvas>

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4 profile-banner-content">
                    <div class="d-flex align-items-center gap-4">
                        <div class="profile-avatar-large anime-avatar">
                            <i class="{{ config('appearance.avatar_icons')[$user->avatar_icon ?? 'person-circle']['class'] ?? 'bi bi-person-circle' }}"></i>
                        </div>
                        <div>
                            <h1 class="fw-black mb-1 text-white" style="font-size: 2.2rem; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">{{ $user->name }}</h1>
                            <p class="text-white text-opacity-90 fw-medium fs-5 mb-0" style="text-shadow: 0 1px 2px rgba(0,0,0,0.2);">
                                <i class="bi bi-envelope-at me-1"></i> {{ $user->email }}
                            </p>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                        <span class="badge bg-white text-primary rounded-pill px-4 py-2 shadow-sm fs-6 d-flex align-items-center fw-bold" style="color: var(--app-primary) !important;">
                            <i class="bi bi-shield-check me-2"></i> {{ $rolPrincipal }}
                        </span>

                        @if($user->acepto_consentimiento)
                            <span class="badge bg-success border border-white border-opacity-25 rounded-pill px-4 py-2 shadow-sm fs-6 d-flex align-items-center fw-bold">
                                <i class="bi bi-check-circle-fill me-2"></i> Consentimiento OK
                            </span>
                        @else
                            <span class="badge bg-warning text-dark border border-white border-opacity-25 rounded-pill px-4 py-2 shadow-sm fs-6 d-flex align-items-center fw-bold">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i> Pendiente
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 anime-item">
            <div class="app-card bg-body-tertiary p-4 p-md-5 h-100 rounded-4 border border-secondary border-opacity-10 shadow-sm hover-elevate">
                <div class="d-flex align-items-center gap-3 border-bottom border-secondary border-opacity-10 pb-3 mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width:45px;height:45px;">
                        <i class="bi bi-journal-richtext fs-5"></i>
                    </div>
                    <h5 class="fw-black text-body mb-0">Resumen de tu cuenta</h5>
                </div>

                <div class="d-flex flex-column gap-4">
                    <div>
                        <small class="text-body-secondary fw-bold text-uppercase" style="letter-spacing: 0.5px;">Username</small>
                        <div class="fw-black text-body fs-5 mt-1">{{ $user->name }}</div>
                    </div>

                    <div>
                        <small class="text-body-secondary fw-bold text-uppercase" style="letter-spacing: 0.5px;">Correo</small>
                        <div class="fw-bold text-body fs-5 mt-1">{{ $user->email }}</div>
                    </div>

                    @if($user->persona)
                        <div>
                            <small class="text-body-secondary fw-bold text-uppercase" style="letter-spacing: 0.5px;">Nombre Real</small>
                            <div class="fw-bold text-body fs-5 mt-1">{{ $user->persona->nombre }} {{ $user->persona->apellido_paterno }}</div>
                        </div>
                    @endif

                    <div class="mt-4 p-4 bg-body rounded-4 border border-secondary border-opacity-10 shadow-sm text-center">
                        <i class="bi bi-calendar2-check text-primary fs-3 d-block mb-2"></i>
                        <small class="text-body-secondary fw-bold text-uppercase d-block mb-1" style="letter-spacing: 0.5px;">Miembro Desde</small>
                        <div class="fw-black text-body fs-4">{{ $user->created_at?->format('d/m/Y') ?? 'N/D' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 anime-item">
            <div class="d-grid gap-4">
                @include('perfil.partials.information-form')
                @include('perfil.partials.persona-form')
                @include('perfil.partials.appearance-form')
                @include('perfil.partials.password-form')
                @include('perfil.partials.delete-account-form')
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/granim.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Animaciones Anime.js
            if(typeof anime !== 'undefined') {
                anime({ targets: '.anime-item', translateY: [30, 0], opacity: [0, 1], delay: anime.stagger(150), easing: 'easeOutExpo', duration: 900 });
                anime({ targets: '.anime-avatar', scale: [1, 1.05, 1], opacity: [0.8, 1, 0.8], duration: 4000, loop: true, easing: 'easeInOutSine' });
            }

            // Efecto Granim complejo de 3 colores con dirección izquierda-derecha para coincidir con el dashboard
            if (document.getElementById('granim-canvas-profile') && typeof Granim !== 'undefined') {
                var granimInstance = new Granim({
                    element: '#granim-canvas-profile',
                    direction: 'left-right', // Horizontal luce mejor con 3 paradas de color
                    isPausedWhenNotInView: true,
                    states : {
                        "default-state": {
                            gradients: [
                                {!! $granimPalettes !!}
                            ],
                            transitionSpeed: 7000
                        }
                    }
                });
            }
        });
    </script>
@endpush
