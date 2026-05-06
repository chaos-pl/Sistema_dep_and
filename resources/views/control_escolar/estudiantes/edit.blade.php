@extends('layouts.app')
@section('title', 'Editar Estudiante - Control Escolar')
@section('page-title', 'Editar Estudiante')
@section('page-subtitle', 'Modificar datos escolares del estudiante')
@section('content')
<div class="app-card p-4 p-md-5 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div><h4 class="fw-black text-body mb-1"><i class="bi bi-pencil-square text-warning me-2"></i>Editar Estudiante</h4><p class="text-body-secondary mb-0">Modifica los datos académicos del estudiante.</p></div>
        <a href="{{ route('control_escolar.estudiantes.index') }}" class="btn btn-light rounded-pill fw-bold shadow-sm px-4"><i class="bi bi-arrow-left me-2"></i>Volver</a>
    </div>
    <form action="{{ route('control_escolar.estudiantes.update', $estudiante) }}" method="POST">@csrf @method('PUT')
        <div class="row g-4">
            <div class="col-12"><h6 class="fw-bold text-primary mb-0"><i class="bi bi-person-fill me-2"></i>Datos Personales</h6><hr class="mt-2 mb-0 border-secondary border-opacity-15"></div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Nombre <span class="text-danger">*</span></label><input type="text" name="nombre" value="{{ old('nombre', $estudiante->persona?->nombre) }}" class="form-control form-control-lg bg-body-tertiary @error('nombre') is-invalid @enderror" required>@error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Apellido Paterno <span class="text-danger">*</span></label><input type="text" name="apellido_paterno" value="{{ old('apellido_paterno', $estudiante->persona?->apellido_paterno) }}" class="form-control form-control-lg bg-body-tertiary @error('apellido_paterno') is-invalid @enderror" required>@error('apellido_paterno')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Apellido Materno</label><input type="text" name="apellido_materno" value="{{ old('apellido_materno', $estudiante->persona?->apellido_materno) }}" class="form-control form-control-lg bg-body-tertiary"></div>

            <div class="col-12 mt-4"><h6 class="fw-bold text-primary mb-0"><i class="bi bi-mortarboard me-2"></i>Datos Escolares</h6><hr class="mt-2 mb-0 border-secondary border-opacity-15"></div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Matrícula <span class="text-danger">*</span></label><input type="text" name="matricula" value="{{ old('matricula', $estudiante->matricula) }}" class="form-control form-control-lg bg-body-tertiary @error('matricula') is-invalid @enderror" required>@error('matricula')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Estado <span class="text-danger">*</span></label><select name="estado" class="form-select form-select-lg bg-body-tertiary" required><option value="activo" {{ old('estado', $estudiante->estado)=='activo'?'selected':'' }}>Activo</option><option value="baja_temporal" {{ old('estado', $estudiante->estado)=='baja_temporal'?'selected':'' }}>Baja Temporal</option><option value="baja_definitiva" {{ old('estado', $estudiante->estado)=='baja_definitiva'?'selected':'' }}>Baja Definitiva</option></select></div>
            <div class="col-md-4">
                <label class="form-label fw-bold text-body-secondary">Grupo Actual</label>
                <div class="form-control-lg bg-body-tertiary form-control text-body-secondary">{{ $estudiante->grupo?->nombre ?? 'Sin grupo' }}</div>
                <small class="text-body-secondary">Para cambiar grupo, usa la función "Cambiar grupo" en la lista de estudiantes.</small>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-3 mt-5 pt-4 border-top border-secondary border-opacity-10">
            <a href="{{ route('control_escolar.estudiantes.index') }}" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm">Cancelar</a>
            <button type="submit" class="btn btn-warning text-dark rounded-pill px-4 fw-bold shadow-sm"><i class="bi bi-arrow-repeat me-2"></i>Actualizar</button>
        </div>
    </form>
</div>
@endsection
