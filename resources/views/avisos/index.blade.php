@extends('layouts.app')
@section('page-title', 'Notificaciones')
@section('content')
<section class="tm-hero mb-4"><span class="tm-eyebrow">Tu bandeja</span><h2>Notificaciones internas</h2><p>Avisos de nuevos casos y retroalimentación. El contenido se consulta únicamente con los permisos vigentes.</p></section>
<div class="d-flex gap-2 mb-4"><a class="btn btn-outline-primary" href="{{ route('avisos.index') }}">Todos</a><a class="btn btn-outline-primary" href="{{ route('avisos.index', ['estado' => 'pendientes']) }}">Sin leer</a></div>
@forelse($avisos as $aviso)
<article class="tm-panel tm-body mb-3 d-flex flex-wrap gap-3 justify-content-between align-items-center">
    <div><span class="badge {{ $aviso->leido_at ? 'text-bg-secondary' : 'text-bg-primary' }}">{{ $aviso->leido_at ? 'Leído' : 'Sin leer' }}</span>
        <h3 class="h5 mt-2">{{ $aviso->titulo }}</h3><time class="small text-body-secondary">{{ $aviso->created_at->format('d/m/Y H:i') }}</time></div>
    <div class="d-flex flex-wrap gap-2">
        <form method="POST" action="{{ route('avisos.open', $aviso) }}">@csrf<button class="btn btn-primary tm-action">Consultar</button></form>
        <form method="POST" action="{{ route('avisos.update', $aviso) }}">@csrf @method('PATCH')<input type="hidden" name="leido" value="{{ $aviso->leido_at ? 0 : 1 }}"><button class="btn btn-outline-secondary">Marcar {{ $aviso->leido_at ? 'sin leer' : 'leído' }}</button></form>
    </div>
</article>
@empty
<section class="tm-panel tm-body"><p class="mb-0">No tienes notificaciones en esta vista.</p></section>
@endforelse
{{ $avisos->links() }}
@endsection
