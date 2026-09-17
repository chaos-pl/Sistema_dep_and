@if($errors->any())<div class="alert alert-danger" role="alert">@foreach($errors->all() as $error)<p class="mb-0">{{ $error }}</p>@endforeach</div>@endif
<form method="GET" class="tm-panel tm-body d-flex flex-wrap gap-3 align-items-end mb-4">
    <div><label for="desde" class="form-label">Desde</label><input class="form-control" type="date" id="desde" name="desde" value="{{ $from->toDateString() }}" required></div>
    <div><label for="hasta" class="form-label">Hasta</label><input class="form-control" type="date" id="hasta" name="hasta" value="{{ $to->toDateString() }}" required></div>
    <button class="btn btn-primary tm-action">Consultar</button>
</form>
