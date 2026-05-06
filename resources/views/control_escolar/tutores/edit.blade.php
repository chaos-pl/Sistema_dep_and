@extends('layouts.app')
@section('title', 'Editar Tutor - Control Escolar')
@section('page-title', 'Editar Tutor')
@section('page-subtitle', 'Modificar datos del tutor')
@section('content')
<div class="app-card p-4 p-md-5 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div><h4 class="fw-black text-body mb-1"><i class="bi bi-pencil-square text-warning me-2"></i>Editar Tutor</h4><p class="text-body-secondary mb-0">Modifica los datos del tutor.</p></div>
        <a href="{{ route('control_escolar.tutores.index') }}" class="btn btn-light rounded-pill fw-bold shadow-sm px-4"><i class="bi bi-arrow-left me-2"></i>Volver</a>
    </div>
    <form action="{{ route('control_escolar.tutores.update', $tutor) }}" method="POST">@csrf @method('PUT')
        <div class="row g-4">
            <div class="col-12"><h6 class="fw-bold text-primary mb-0"><i class="bi bi-person-fill me-2"></i>Datos Personales</h6><hr class="mt-2 mb-0 border-secondary border-opacity-15"></div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Nombre <span class="text-danger">*</span></label><input type="text" name="nombre" value="{{ old('nombre', $tutor->persona?->nombre) }}" class="form-control form-control-lg bg-body-tertiary @error('nombre') is-invalid @enderror" required>@error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Apellido Paterno <span class="text-danger">*</span></label><input type="text" name="apellido_paterno" value="{{ old('apellido_paterno', $tutor->persona?->apellido_paterno) }}" class="form-control form-control-lg bg-body-tertiary @error('apellido_paterno') is-invalid @enderror" required>@error('apellido_paterno')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Apellido Materno</label><input type="text" name="apellido_materno" value="{{ old('apellido_materno', $tutor->persona?->apellido_materno) }}" class="form-control form-control-lg bg-body-tertiary"></div>

            <div class="col-12 mt-4"><h6 class="fw-bold text-primary mb-0"><i class="bi bi-building me-2"></i>Datos Institucionales</h6><hr class="mt-2 mb-0 border-secondary border-opacity-15"></div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Número de Empleado <span class="text-danger">*</span></label><input type="text" name="numero_empleado" value="{{ old('numero_empleado', $tutor->numero_empleado) }}" class="form-control form-control-lg bg-body-tertiary @error('numero_empleado') is-invalid @enderror" required>@error('numero_empleado')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Correo Electrónico <span class="text-danger">*</span></label><input type="email" name="email" value="{{ old('email', $tutor->persona?->user?->email) }}" class="form-control form-control-lg bg-body-tertiary @error('email') is-invalid @enderror" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        </div>
        <div class="d-flex justify-content-end gap-3 mt-5 pt-4 border-top border-secondary border-opacity-10">
            <a href="{{ route('control_escolar.tutores.index') }}" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm">Cancelar</a>
            <button type="submit" class="btn btn-warning text-dark rounded-pill px-4 fw-bold shadow-sm"><i class="bi bi-arrow-repeat me-2"></i>Actualizar</button>
        </div>
    </form>
</div>
@endsection
