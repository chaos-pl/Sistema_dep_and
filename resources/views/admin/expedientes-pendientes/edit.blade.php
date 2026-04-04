@extends('layouts.app')

@section('title', 'Completar expediente - PROMETEO')
@section('page-title', 'Completar Expediente')
@section('page-subtitle', 'Asignación de grupo y matrícula al estudiante')

@push('styles')
    <style>
        .anime-item { opacity: 0; transform: translateY(20px); }
    </style>
@endpush

@section('content')
    <div class="row justify-content-center anime-item">
        <div class="col-lg-8">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4">

                <div class="d-flex align-items-center gap-3 mb-4 border-bottom border-secondary border-opacity-10 pb-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-person-lines-fill fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-black mb-1 text-body">Asignación Académica</h4>
                        <p class="text-body-secondary mb-0 small">
                            Completando el expediente del usuario: <strong class="text-primary">{{ $user->name }}</strong>
                        </p>
                    </div>
                </div>

                <div class="bg-body-tertiary rounded-4 p-3 mb-4 border border-secondary border-opacity-10 d-flex align-items-center gap-3">
                    <div class="bg-body rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                        <i class="bi bi-envelope-at text-info"></i>
                    </div>
                    <div>
                        <small class="text-body-secondary d-block fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">CORREO REGISTRADO</small>
                        <span class="text-body fw-medium">{{ $user->email }}</span>
                    </div>
                </div>

                <form action="{{ route('admin.expedientes-pendientes.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-body-secondary">Matrícula Escolar</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary"><i class="bi bi-hash"></i></span>
                                <input type="text" name="matricula" value="{{ old('matricula') }}"
                                       class="form-control form-control-lg bg-body-tertiary border-start-0 ps-0" placeholder="Ej. 20261001" required>
                            </div>
                            @error('matricula') <small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-body-secondary">Grupo Asignado</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary"><i class="bi bi-collection-fill"></i></span>
                                <select name="grupo_id" class="form-select form-select-lg bg-body-tertiary border-start-0 ps-0" required>
                                    <option value="">Selecciona un grupo...</option>
                                    @foreach($grupos as $grupo)
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

                    <div class="d-flex justify-content-between mt-5 pt-4 border-top border-secondary border-opacity-10">
                        <a href="{{ route('admin.expedientes-pendientes.index') }}" class="btn btn-light border px-4 rounded-pill fw-bold text-body-secondary shadow-sm">
                            <i class="bi bi-arrow-left me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm">
                            <i class="bi bi-save me-2"></i>Guardar Expediente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
        });
    </script>
@endpush
