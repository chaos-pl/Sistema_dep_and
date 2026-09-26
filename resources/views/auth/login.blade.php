@extends('layouts.guest')
@section('title', 'Iniciar sesión | PROMETEO')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}?v={{ filemtime(public_path('css/login.css')) }}">
@endpush

@section('content')
    <main class="login" data-login-scene>

        {{-- Atmósfera con los azules, turquesas y ámbar del logo original. --}}
        <div class="login__backdrop" aria-hidden="true">
            <x-auth-logo-background />
            <span class="login__aurora login__aurora--deep"></span>
            <span class="login__aurora login__aurora--cyan"></span>
            <span class="login__aurora login__aurora--dawn"></span>


        </div>

        <div class="login__grid">

            <section class="login__story" aria-labelledby="welcome-title">
                <a href="{{ url('/') }}" class="login__brand login-anim" data-anim="brand">
                    <img class="login__emblem" src="{{ asset('img/logo_prometeo.png') }}" width="112" height="112" alt="">
                    <span class="login__wordmark">PROMETEO<small>Monitoreo emocional</small></span>
                </a>

                <div class="login__copy">
                    <h1 id="welcome-title" class="login-anim" data-anim="title">Tu bienestar merece<br>un espacio propio.</h1>

                    <p class="login__purpose login-anim" data-anim="purpose">Sistema web para la detección oportuna de depresión y ansiedad estudiantil</p>

                    <p class="login__lead login-anim" data-anim="lead">Reconoce cómo te sientes, observa tu evolución durante el semestre y encuentra acompañamiento dentro de tu comunidad universitaria.</p>

                    <ul class="login__pills login-anim" data-anim="pills">
                        <li>Tamizajes y seguimiento</li>
                        <li>Acompañamiento tutorial</li>
                        <li>Atención psicológica</li>
                    </ul>
                </div>

                <p class="login__note login-anim" data-anim="note">PROMETEO acompaña y detecta a tiempo. No sustituye la atención profesional.</p>
            </section>

            <section class="login__access" aria-labelledby="login-heading">
                <div class="login-card login__card login-anim" data-anim="card">

                    <div class="login__card-head">
                        <h2 id="login-heading">Inicia sesión</h2>
                        <p class="login__muted">Ingresa a tu espacio en PROMETEO.</p>
                    </div>

                    @if(session('status'))
                        <div class="login__status" role="status">{{ session('status') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="login__errors" role="alert" id="login-errors">
                            <i class="bi bi-exclamation-circle" aria-hidden="true"></i>
                            <div>
                                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" data-login-form>
                        @csrf

                        <div class="login__field login-anim" data-anim="row">
                            <label for="email">Correo electrónico</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" inputmode="email" spellcheck="false" required autofocus placeholder="tu@correo.com" @if($errors->has('email')) aria-invalid="true" aria-describedby="login-errors" @endif>
                        </div>

                        <div class="login__field login-anim" data-anim="row">
                            <label for="password">Contraseña</label>
                            <div class="login__password">
                                <input id="password" name="password" type="password" autocomplete="current-password" required placeholder="Tu contraseña" @if($errors->has('password')) aria-invalid="true" aria-describedby="login-errors" @endif>
                                <button type="button" class="login__toggle" data-toggle-password aria-controls="password" aria-pressed="false">Mostrar</button>
                            </div>
                        </div>

                        <div class="login__options login-anim" data-anim="row">
                            <label class="login__remember">
                                <input type="checkbox" name="remember" @checked(old('remember'))>
                                <span class="login__box" aria-hidden="true"></span>
                                <span>Recordarme</span>
                            </label>
                            @if(Route::has('password.request'))
                                <a class="login__quiet" href="{{ route('password.request') }}">Recuperar contraseña</a>
                            @endif
                        </div>

                        <button type="submit" class="login-submit login__submit login-anim" data-anim="row">Iniciar sesión</button>
                    </form>

                    @if(Route::has('register'))
                        <p class="login__register login-anim" data-anim="row">¿Es tu primera visita? <a href="{{ route('register') }}">Crear cuenta</a></p>
                    @endif

                    <p class="login__footnote login-anim" data-anim="row">Un paso a la vez. Estamos para acompañarte.</p>
                </div>
            </section>
        </div>
    </main>
@endsection
