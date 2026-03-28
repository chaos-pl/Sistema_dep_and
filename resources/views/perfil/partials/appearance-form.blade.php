@php
    $appearance = $user->appearance;

    // Agregamos fallbacks vacíos solo por seguridad en caso de que config devuelva null
    $avatarIcons = config('appearance.avatar_icons') ?? [];
    $themes = config('appearance.themes') ?? ['light' => 'Claro', 'dark' => 'Oscuro'];
    $accentColors = config('appearance.accent_colors') ?? ['purple' => 'Morado'];
    $densities = config('appearance.density') ?? ['comfortable' => 'Cómoda'];
@endphp

<div class="app-card p-4 p-md-5 rounded-4 border-0 shadow-sm mt-4">
    <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
        <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <i class="bi bi-palette fs-4"></i>
        </div>
        <div>
            <h5 class="fw-black mb-1 text-dark">Apariencia del Sistema</h5>
            <p class="text-muted mb-0 small">Personaliza cómo se ve tu cuenta dentro de PROMETEO.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('perfil.appearance.update') }}">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="form-label fw-bold text-secondary d-block mb-3">Icono de perfil</label>
            <div class="row g-3">
                @foreach($avatarIcons as $key => $icon)
                    <div class="col-6 col-md-3 col-xl-2">
                        <label class="appearance-icon-card w-100">
                            <input type="radio" name="avatar_icon" value="{{ $key }}" class="d-none appearance-icon-input" {{ old('avatar_icon', $user->avatar_icon) === $key ? 'checked' : '' }}>
                            <div class="appearance-icon-option text-center p-3 rounded-4 border bg-light shadow-sm">
                                <div class="mb-2">
                                    <i class="{{ $icon['class'] }} fs-2"></i>
                                </div>
                                <div class="fw-bold small text-secondary">{{ $icon['label'] }}</div>
                            </div>
                        </label>
                    </div>
                @endforeach
            </div>
            @if($errors->updateAppearance->has('avatar_icon'))
                <small class="text-danger fw-bold d-block mt-2">{{ $errors->updateAppearance->first('avatar_icon') }}</small>
            @endif
        </div>

        <div class="row g-4 mb-4 border-top pt-4">
            <div class="col-md-4">
                <label for="theme" class="form-label fw-bold text-secondary">Tema del Sistema</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-moon-stars"></i></span>
                    <select name="theme" id="theme" class="form-select form-select-lg bg-light border-start-0 ps-0">
                        @foreach($themes as $key => $label)
                            <option value="{{ $key }}" {{ old('theme', $appearance['theme']) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <label for="accent_color" class="form-label fw-bold text-secondary">Color de Acento</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-brush"></i></span>
                    <select name="accent_color" id="accent_color" class="form-select form-select-lg bg-light border-start-0 ps-0">
                        @foreach($accentColors as $key => $label)
                            <option value="{{ $key }}" {{ old('accent_color', $appearance['accent_color']) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <label for="density" class="form-label fw-bold text-secondary">Distribución Visual</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-aspect-ratio"></i></span>
                    <select name="density" id="density" class="form-select form-select-lg bg-light border-start-0 ps-0">
                        @foreach($densities as $key => $label)
                            <option value="{{ $key }}" {{ old('density', $appearance['density']) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="p-3 bg-light rounded-4 border border-secondary border-opacity-10 d-flex align-items-center gap-3">
            <div class="form-check form-switch m-0 fs-4">
                <input class="form-check-input shadow-none" type="checkbox" name="reduced_motion" id="reduced_motion" value="1" {{ old('reduced_motion', $appearance['reduced_motion']) ? 'checked' : '' }}>
            </div>
            <label class="form-check-label fw-bold text-secondary mb-0" style="cursor: pointer;" for="reduced_motion">
                Reducir animaciones y transiciones <br>
                <small class="text-muted fw-normal">Activa esta opción si notas que el sistema funciona lento en tu dispositivo.</small>
            </label>
        </div>

        <div class="mt-4 text-end border-top pt-3">
            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-stars me-2"></i>Guardar Apariencia
            </button>
        </div>
    </form>
</div>
