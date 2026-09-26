@props(['user'])
@if($user?->persona?->foto_perfil && preg_match('/\Aavatars\/[a-f0-9-]+\.jpg\z/', $user->persona->foto_perfil))
    <img src="{{ route('perfil.photo.show') }}" class="profile-photo" alt="Tu foto de perfil" width="144" height="144">
@else
    <span class="profile-initials" aria-label="Perfil de {{ $user?->name }}">{{ collect(explode(' ', trim($user?->name ?? 'Usuario')))->filter()->take(2)->map(fn($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('') }}</span>
@endif
