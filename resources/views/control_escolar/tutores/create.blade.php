@extends('layouts.app')
@section('title', 'Nuevo Tutor - Control Escolar')
@section('page-title', 'Registrar Tutor')
@section('page-subtitle', 'Alta de un nuevo tutor en el sistema')
@section('content')
<div class="app-card p-4 p-md-5 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div><h4 class="fw-black text-body mb-1"><i class="bi bi-person-video3 text-primary me-2"></i>Nuevo Tutor</h4><p class="text-body-secondary mb-0">Completa los datos para registrar un nuevo tutor.</p></div>
        <a href="{{ route('control_escolar.tutores.index') }}" class="btn btn-light rounded-pill fw-bold shadow-sm px-4"><i class="bi bi-arrow-left me-2"></i>Volver</a>
    </div>
    <form action="{{ route('control_escolar.tutores.store') }}" method="POST">@csrf
        <div class="row g-4">
            <div class="col-12"><h6 class="fw-bold text-primary mb-0"><i class="bi bi-person-fill me-2"></i>Datos Personales</h6><hr class="mt-2 mb-0 border-secondary border-opacity-15"></div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Nombre <span class="text-danger">*</span></label><input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control form-control-lg bg-body-tertiary @error('nombre') is-invalid @enderror" required>@error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Apellido Paterno <span class="text-danger">*</span></label><input type="text" name="apellido_paterno" value="{{ old('apellido_paterno') }}" class="form-control form-control-lg bg-body-tertiary @error('apellido_paterno') is-invalid @enderror" required>@error('apellido_paterno')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Apellido Materno</label><input type="text" name="apellido_materno" value="{{ old('apellido_materno') }}" class="form-control form-control-lg bg-body-tertiary"></div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Fecha de Nacimiento <span class="text-danger">*</span></label><input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" class="form-control form-control-lg bg-body-tertiary @error('fecha_nacimiento') is-invalid @enderror" required>@error('fecha_nacimiento')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Género <span class="text-danger">*</span></label><select name="genero" class="form-select form-select-lg bg-body-tertiary @error('genero') is-invalid @enderror" required><option value="">Seleccionar...</option><option value="masculino" {{ old('genero')=='masculino'?'selected':'' }}>Masculino</option><option value="femenino" {{ old('genero')=='femenino'?'selected':'' }}>Femenino</option><option value="otro" {{ old('genero')=='otro'?'selected':'' }}>Otro</option><option value="prefiero_no_decirlo" {{ old('genero')=='prefiero_no_decirlo'?'selected':'' }}>Prefiero no decirlo</option></select>@error('genero')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Teléfono</label><input type="text" name="telefono" value="{{ old('telefono') }}" class="form-control form-control-lg bg-body-tertiary"></div>

            <div class="col-12 mt-4"><h6 class="fw-bold text-primary mb-0"><i class="bi bi-building me-2"></i>Datos Institucionales</h6><hr class="mt-2 mb-0 border-secondary border-opacity-15"></div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Número de Empleado <span class="text-danger">*</span></label><input type="text" name="numero_empleado" value="{{ old('numero_empleado') }}" class="form-control form-control-lg bg-body-tertiary @error('numero_empleado') is-invalid @enderror" required>@error('numero_empleado')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>

            <div class="col-12 mt-4"><h6 class="fw-bold text-primary mb-0"><i class="bi bi-shield-lock me-2"></i>Credenciales de Acceso</h6><hr class="mt-2 mb-0 border-secondary border-opacity-15"></div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Correo electrónico <span class="text-danger">*</span></label><input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg bg-body-tertiary @error('email') is-invalid @enderror" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Contraseña <span class="text-danger">*</span></label><input type="password" name="password" class="form-control form-control-lg bg-body-tertiary @error('password') is-invalid @enderror" required>@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Confirmar Contraseña <span class="text-danger">*</span></label><input type="password" name="password_confirmation" class="form-control form-control-lg bg-body-tertiary" required></div>
        </div>
        <div class="d-flex justify-content-end gap-3 mt-5 pt-4 border-top border-secondary border-opacity-10">
            <a href="{{ route('control_escolar.tutores.index') }}" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm">Cancelar</a>
            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm"><i class="bi bi-floppy-fill me-2"></i>Registrar Tutor</button>
        </div>
    </form>
</div>
@endsection
