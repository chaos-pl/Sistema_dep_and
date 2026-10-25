<div class="mb-4">
    <label for="tipo-registro-psicologo" class="form-label fw-bold">¿Cómo deseas registrar al psicólogo?</label>
    <select id="tipo-registro-psicologo" name="tipo_registro" class="form-select" data-registration-mode>
        <option value="nueva" @selected(old('tipo_registro', 'nueva') === 'nueva')>Registrar persona nueva</option>
        <option value="existente" @selected(old('tipo_registro') === 'existente')>Seleccionar persona existente</option>
    </select>
</div>
<fieldset data-existing-person @disabled(old('tipo_registro') !== 'existente') @if(old('tipo_registro') !== 'existente') hidden @endif>
    <div class="mb-3">
        <label for="buscar-persona-psicologo" class="form-label">Buscar por nombre o correo</label>
        <input id="buscar-persona-psicologo" type="search" class="form-control" data-person-search placeholder="Escribe para filtrar las personas">
    </div>
    <div class="mb-3">
        <label for="persona-psicologo" class="form-label fw-bold">Persona existente</label>
        <select id="persona-psicologo" name="persona_id" class="form-select" data-person-select required>
            <option value="">Selecciona una persona</option>
            @foreach($personasDisponibles as $personaDisponible)
                <option value="{{ $personaDisponible->id }}" data-phone="{{ $personaDisponible->telefono }}" data-roles="{{ $personaDisponible->user->roles->pluck('name')->join(', ') }}" @selected((string) old('persona_id') === (string) $personaDisponible->id)>
                    {{ trim($personaDisponible->nombre.' '.$personaDisponible->apellido_paterno.' '.$personaDisponible->apellido_materno) }} — {{ $personaDisponible->user->email }}
                </option>
            @endforeach
        </select>
        <small class="text-body-secondary">Solo se muestran personas con cuenta y sin perfil de psicólogo.</small>
        @error('persona_id')<div class="text-danger">{{ $message }}</div>@enderror
        <p class="mt-2 mb-0" data-person-summary aria-live="polite"></p>
    </div>
    <p class="text-body-secondary">Se conservarán su cuenta, contraseña y roles actuales. Se agregará el rol de psicólogo.</p>
    <div class="mb-3">
        <label for="telefono-persona-psicologo" class="form-label">Teléfono (opcional)</label>
        <input id="telefono-persona-psicologo" name="telefono" value="{{ old('telefono') }}" data-person-phone class="form-control" inputmode="numeric" pattern="[0-9]{10}" minlength="10" maxlength="10">
        <small class="text-body-secondary">Si ya tiene teléfono se conserva. Si falta, puedes completar sus 10 dígitos.</small>
        @error('telefono')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label for="cedula-persona-psicologo" class="form-label fw-bold">Cédula profesional</label>
        <input id="cedula-persona-psicologo" name="cedula_profesional" value="{{ old('cedula_profesional') }}" class="form-control" maxlength="50" required>
        @error('cedula_profesional')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
</fieldset>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const mode = document.querySelector('[data-registration-mode]');
    if (!mode) return;
    const form = mode.closest('form');
    const existing = form.querySelector('[data-existing-person]');
    const fresh = form.querySelector('[data-new-person]');
    const select = form.querySelector('[data-person-select]');
    const phone = form.querySelector('[data-person-phone]');
    const summary = form.querySelector('[data-person-summary]');
    const toggle = () => {
        const useExisting = mode.value === 'existente';
        existing.hidden = existing.disabled = !useExisting;
        fresh.hidden = fresh.disabled = useExisting;
    };
    const showPerson = (keepInput = false) => {
        const option = select.selectedOptions[0];
        const storedPhone = option?.dataset.phone || '';
        phone.disabled = !!storedPhone;
        if (storedPhone || !keepInput) phone.value = storedPhone;
        summary.textContent = option?.value ? 'Roles actuales: ' + (option.dataset.roles || 'Sin roles') : '';
    };
    mode.addEventListener('change', toggle);
    select.addEventListener('change', () => showPerson());
    form.querySelector('[data-person-search]').addEventListener('input', event => {
        const query = event.target.value.toLocaleLowerCase().trim();
        for (const option of select.options) {
            option.hidden = !!option.value && !option.selected && !option.textContent.toLocaleLowerCase().includes(query);
        }
    });
    toggle();
    showPerson(true);
    @if($errors->any() && old('tipo_registro'))
        const modal = form.closest('.modal');
        if (modal) bootstrap.Modal.getOrCreateInstance(modal).show();
    @endif
});
</script>
@endpush
