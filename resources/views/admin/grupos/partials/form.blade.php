@csrf
@if(isset($method) && strtoupper($method) !== 'POST')
    @method($method)
@endif

<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label fw-bold text-body-secondary">Carrera vinculada</label>
        <div class="input-group">
            <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary"><i class="bi bi-mortarboard-fill"></i></span>
            <select name="carrera_id" class="form-select form-select-lg bg-body-tertiary border-start-0 ps-0" required>
                <option value="">Selecciona una carrera...</option>
                @foreach($carreras as $carrera)
                    <option value="{{ $carrera->id }}" @selected(old('carrera_id', $grupo->carrera_id ?? '') == $carrera->id)>
                        {{ $carrera->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
        @error('carrera_id') <small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-bold text-body-secondary">Tutor asignado</label>
        <div class="input-group">
            <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary"><i class="bi bi-person-video3"></i></span>
            <select name="tutor_id" class="form-select form-select-lg bg-body-tertiary border-start-0 ps-0" required>
                <option value="">Selecciona un tutor...</option>
                @foreach($tutores as $tutor)
                    <option value="{{ $tutor->id }}" @selected(old('tutor_id', $grupo->tutor_id ?? '') == $tutor->id)>
                        {{ $tutor->persona->nombre ?? '' }} {{ $tutor->persona->apellido_paterno ?? '' }} {{ $tutor->persona->apellido_materno ?? '' }} — {{ $tutor->numero_empleado }}
                    </option>
                @endforeach
            </select>
        </div>
        @error('tutor_id') <small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-bold text-body-secondary">Nombre del grupo</label>
        <div class="input-group">
            <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary"><i class="bi bi-collection-fill"></i></span>
            <input type="text" name="nombre" value="{{ old('nombre', $grupo->nombre ?? '') }}" class="form-control form-control-lg bg-body-tertiary border-start-0 ps-0" placeholder="Ej. 401-A" required>
        </div>
        @error('nombre') <small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-bold text-body-secondary">Periodo escolar</label>
        <div class="input-group">
            <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary"><i class="bi bi-calendar3"></i></span>
            <input type="text" name="periodo" value="{{ old('periodo', $grupo->periodo ?? '') }}" class="form-control form-control-lg bg-body-tertiary border-start-0 ps-0" placeholder="Ej. 2026-A" required>
        </div>
        @error('periodo') <small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small> @enderror
    </div>
</div>

<div class="d-flex justify-content-between mt-5 pt-4 border-top border-secondary border-opacity-10">
    <a href="{{ route('admin.grupos.index') }}" class="btn btn-light border px-4 rounded-pill fw-bold text-body-secondary shadow-sm">
        <i class="bi bi-arrow-left me-2"></i>Cancelar
    </a>
    <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm">
        <i class="bi bi-save me-2"></i>{{ $submitText }}
    </button>
</div>
