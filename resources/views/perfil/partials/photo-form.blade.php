<section class="mb-4 pb-4 border-bottom" aria-labelledby="photo-heading">
    <h6 id="photo-heading" class="fw-bold">Foto de perfil</h6>
    <p class="text-muted small">JPEG, PNG o WebP, hasta 2 MB y 3000 × 3000 píxeles. Ajusta el encuadre antes de guardar.</p>
    @if(session('photo_status'))<p role="status" class="text-success">{{ session('photo_status') }}</p>@endif
    @error('photo')<p role="alert" class="text-danger">{{ $message }}</p>@enderror
    @if($user->persona)
    <form method="POST" action="{{ route('perfil.photo.store') }}" enctype="multipart/form-data" data-photo-form>
        @csrf
        <div class="d-flex flex-wrap gap-4 align-items-center">
            <div class="photo-preview" data-photo-preview><x-profile-avatar :user="$user" /></div>
            <div class="flex-grow-1">
                <label for="profile_photo" class="form-label">Elegir una imagen</label>
                <input type="file" id="profile_photo" name="photo" accept="image/jpeg,image/png,image/webp" class="form-control" required>
                <label class="form-label mt-2" for="position_x">Encuadre horizontal</label>
                <input class="form-range" type="range" id="position_x" name="position_x" min="0" max="100" value="50">
                <label class="form-label" for="position_y">Encuadre vertical</label>
                <input class="form-range" type="range" id="position_y" name="position_y" min="0" max="100" value="50">
                <p class="small text-muted" data-photo-status role="status">Se guardará una imagen cuadrada de 512 × 512 píxeles.</p>
                <button class="btn btn-primary" type="submit">Guardar foto</button>
            </div>
        </div>
    </form>
    @if($user->persona->foto_perfil)
        <form method="POST" action="{{ route('perfil.photo.destroy') }}" class="mt-3">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger" type="submit">Eliminar foto</button>
        </form>
    @endif
    @else
        <p class="small">Completa y guarda tus datos personales para habilitar la carga de tu foto.</p>
    @endif
</section>
