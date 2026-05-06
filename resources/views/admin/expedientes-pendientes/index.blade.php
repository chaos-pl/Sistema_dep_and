@extends('layouts.app')

@section('title', 'Estudiantes sin expediente - PROMETEO')
@section('page-title', 'Estudiantes sin expediente')
@section('page-subtitle', 'Usuarios con rol estudiante pendientes de asignación académica')

@push('styles')
    <style>
        .search-input-prometeo { padding-left: 2.8rem !important; }
        .anime-item { opacity: 0; transform: translateY(20px); }
        .hover-elevate { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .hover-elevate:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important; }

        /* --- DISEÑO PREMIUM PARA MODALES --- */
        .modal-content {
            border-radius: 1.5rem;
            overflow: hidden;
            border: 0;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .modal-header-custom {
            background-color: var(--app-primary);
            color: white;
            border-bottom: 0;
            padding: 1.5rem 2rem;
        }

        .modal-body-custom {
            padding: 2rem;
            background-color: var(--app-surface);
        }

        .modal-footer-custom {
            padding: 1.5rem 2rem;
            border-top: 1px solid rgba(148, 163, 184, 0.15);
            background-color: rgba(148, 163, 184, 0.05);
        }

        /* Ajustes Modo Oscuro para Modales */
        body.theme-dark .modal-content, body.theme-system .modal-content {
            background-color: #1e293b !important;
            border: 1px solid rgba(255,255,255,0.1);
        }
        body.theme-dark .modal-body-custom, body.theme-system .modal-body-custom {
            background-color: #0f172a !important;
        }
        body.theme-dark .modal-footer-custom, body.theme-system .modal-footer-custom {
            background-color: #1e293b !important;
            border-color: rgba(255,255,255,0.1) !important;
        }

        /* Animación de Despliegue */
        .modal.fade {
            perspective: 2000px;
        }

        .modal.fade .modal-dialog {
            opacity: 0;
            transform-origin: center center;
            transform: translateZ(-500px) rotateY(90deg) scale(0.5);
            transition: transform 0.7s cubic-bezier(0.165, 0.84, 0.44, 1), opacity 0.4s ease-in-out;
        }

        .modal.show .modal-dialog {
            opacity: 1;
            transform: translateZ(0) rotateY(0deg) scale(1);
        }

        .modal-backdrop.show {
            opacity: 0.75;
            backdrop-filter: blur(8px) brightness(0.4);
            background-color: #000000;
        }
    </style>
@endpush

@section('content')
    @php
        $gruposForm = \App\Models\Grupo::with('carrera')->orderBy('nombre')->get();
    @endphp

    <div class="row g-4 anime-item">
        <div class="col-12">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4">

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 border-bottom border-secondary border-opacity-10 pb-4 mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-warning bg-opacity-10 text-warning-emphasis rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-person-exclamation fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-black mb-1 text-body">Expedientes Incompletos</h4>
                            <p class="text-body-secondary mb-0 small">Usuarios con rol de estudiante que aún no tienen matrícula ni grupo asignado.</p>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-prometeo border-top border-secondary border-opacity-10">
                        <thead class="bg-body-tertiary">
                        <tr>
                            <th class="py-3 px-4 rounded-start-3 text-body-secondary fw-bold border-0">Cuenta de Usuario</th>
                            <th class="py-3 text-body-secondary fw-bold border-0">Correo Electrónico</th>
                            <th class="py-3 text-body-secondary fw-bold border-0">Expediente (Persona)</th>
                            <th class="py-3 px-4 rounded-end-3 text-end text-body-secondary fw-bold border-0">Acción Requerida</th>
                        </tr>
                        </thead>
                        <tbody class="border-top-0">
                        @forelse($usuarios as $usuario)
                            <tr class="border-bottom border-secondary border-opacity-10">
                                <td class="px-4 py-3 fw-bold text-body border-0">
                                    <div class="d-flex align-items-center gap-2" style="transition: transform 0.2s ease;">
                                        <i class="bi bi-person-circle text-primary"></i> {{ $usuario->name }}
                                    </div>
                                </td>

                                <td class="py-3 text-body-secondary fw-medium border-0">
                                    <i class="bi bi-envelope-at me-1 opacity-50"></i> {{ $usuario->email }}
                                </td>

                                <td class="py-3 border-0">
                                    @if($usuario->persona)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2 fw-medium">
                                            <i class="bi bi-check-circle-fill me-1"></i>
                                            {{ $usuario->persona->nombre }}
                                            {{ $usuario->persona->apellido_paterno }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-2 fw-medium">
                                            <i class="bi bi-x-circle-fill me-1"></i> Faltan datos personales
                                        </span>
                                    @endif
                                </td>

                                <td class="text-end px-4 py-3 border-0">
                                    @if($usuario->persona)
                                        <button type="button" class="btn btn-sm btn-primary rounded-pill shadow-sm fw-bold px-3 hover-elevate" data-bs-toggle="modal" data-bs-target="#modalCompletar{{ $usuario->id }}">
                                            Completar Expediente <i class="bi bi-arrow-right-circle ms-1"></i>
                                        </button>
                                    @else
                                        <a href="{{ route('admin.personas.index') }}"
                                           class="btn btn-sm btn-light border text-warning-emphasis rounded-pill shadow-sm fw-bold px-3 hover-elevate">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Vincular Persona Primero
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-body-secondary border-0">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-check2-all fs-1 text-success opacity-50 mb-3"></i>
                                        <span>¡Excelente! Todos los estudiantes tienen su expediente completo.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($usuarios, 'links'))
                    <div class="mt-4">
                        {{ $usuarios->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modales para Completar Expediente -->
    @foreach($usuarios as $usuario)
        @if($usuario->persona)
            <div class="modal fade" id="modalCompletar{{ $usuario->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                    <div class="modal-content">
                        <div class="modal-header modal-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="modal-title fw-black mb-0"><i class="bi bi-person-lines-fill me-2"></i>Asignación Académica</h5>
                            <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <form action="{{ route('admin.expedientes-pendientes.update', $usuario->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body modal-body-custom">
                                <div class="bg-body-tertiary rounded-4 p-3 mb-4 border border-secondary border-opacity-10 d-flex align-items-center gap-3">
                                    <div class="bg-body rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                                        <i class="bi bi-envelope-at text-info"></i>
                                    </div>
                                    <div>
                                        <small class="text-body-secondary d-block fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">COMPLETANDO EXPEDIENTE DEL USUARIO</small>
                                        <span class="text-body fw-medium">{{ $usuario->name }} ({{ $usuario->email }})</span>
                                    </div>
                                </div>

                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-body-secondary">Matrícula Escolar</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-body-tertiary border-end-0 text-muted"><i class="bi bi-hash"></i></span>
                                            <input type="text" name="matricula" value="{{ old('matricula') }}"
                                                   class="form-control bg-body-tertiary form-control-lg border-start-0 ps-0" placeholder="Ej. 20261001" required>
                                        </div>
                                        @error('matricula') <small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-body-secondary">Grupo Asignado</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-body-tertiary border-end-0 text-muted"><i class="bi bi-collection-fill"></i></span>
                                            <select name="grupo_id" class="form-select bg-body-tertiary form-control-lg border-start-0 ps-0" required>
                                                <option value="">Selecciona un grupo...</option>
                                                @foreach($gruposForm as $grupo)
                                                    <option value="{{ $grupo->id }}" @selected(old('grupo_id') == $grupo->id)>
                                                        {{ $grupo->nombre }} | {{ $grupo->periodo }}
                                                        | {{ $grupo->carrera->nombre ?? 'Sin carrera' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('grupo_id') <small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer modal-footer-custom d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm"><i class="bi bi-save me-2"></i>Guardar Expediente</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            anime({
                targets: '.anime-item',
                translateY: [30, 0],
                opacity: [0, 1],
                delay: 100,
                easing: 'easeOutExpo',
                duration: 900
            });

            // FIX: EVITAR QUE LA PANTALLA SE CONGELE CON LOS MODALES
            document.querySelectorAll('.modal').forEach(modal => {
                document.body.appendChild(modal);
            });
        });
    </script>
@endpush
