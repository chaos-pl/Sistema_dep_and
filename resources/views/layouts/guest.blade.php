<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'PROMETEO') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root{
            --app-bg-1:#f7f2ff;
            --app-bg-2:#fdfcff;
            --app-primary:#9b72cf;
            --app-primary-dark:#7f58b7;
            --app-secondary:#b99ae7;
            --app-text:#374151;
            --app-muted:#6b7280;
            --app-border:#eadff7;
            --app-surface:#ffffff;
            --app-glow:rgba(155,114,207,.22);
        }

        *{ box-sizing:border-box; }

        body{
            min-height:100vh;
            margin:0;
            font-family:"Inter","Segoe UI",sans-serif;
            color:var(--app-text);
            background:
                radial-gradient(circle at top left, rgba(185,154,231,.35), transparent 35%),
                radial-gradient(circle at bottom right, rgba(155,114,207,.18), transparent 30%),
                linear-gradient(135deg, var(--app-bg-1) 0%, var(--app-bg-2) 100%);
            overflow-x:hidden;
        }

        .auth-page{
            position:relative;
            min-height:100vh;
        }

        .floating-shape{
            position:absolute;
            border-radius:50%;
            filter:blur(6px);
            opacity:.55;
            animation: floatY 7s ease-in-out infinite;
        }

        .shape-1{
            width:180px;
            height:180px;
            background:rgba(185,154,231,.28);
            top:7%;
            left:6%;
        }

        .shape-2{
            width:120px;
            height:120px;
            background:rgba(155,114,207,.24);
            bottom:12%;
            right:8%;
            animation-delay:1.2s;
        }

        .shape-3{
            width:90px;
            height:90px;
            background:rgba(155,114,207,.18);
            top:22%;
            right:20%;
            animation-delay:2s;
        }

        @keyframes floatY{
            0%,100%{ transform:translateY(0px); }
            50%{ transform:translateY(-16px); }
        }

        .auth-shell{
            position:relative;
            z-index:2;
        }

        .auth-card{
            background:rgba(255,255,255,.78);
            border:1px solid rgba(255,255,255,.65);
            border-radius:28px;
            box-shadow:
                0 20px 60px rgba(155,114,207,.15),
                0 8px 20px rgba(0,0,0,.04);
            backdrop-filter:blur(18px);
        }

        .auth-side{
            background:
                radial-gradient(circle at top left, rgba(255,255,255,.24), transparent 35%),
                linear-gradient(135deg, #9b72cf 0%, #b99ae7 100%);
            position:relative;
            overflow:hidden;
        }

        .auth-side::before,
        .auth-side::after{
            content:"";
            position:absolute;
            border-radius:50%;
            background:rgba(255,255,255,.12);
        }

        .auth-side::before{
            width:220px;
            height:220px;
            top:-70px;
            right:-40px;
        }

        .auth-side::after{
            width:160px;
            height:160px;
            bottom:-50px;
            left:-30px;
        }

        .brand-icon{
            width:74px;
            height:74px;
            border-radius:22px;
        }

        .form-control{
            border-radius:18px;
            border:1px solid var(--app-border);
            padding:.95rem 1rem;
            transition:.25s ease;
        }

        .form-control:focus{
            border-color:#c5aae9;
            box-shadow:0 0 0 .25rem rgba(155,114,207,.14);
        }

        .input-group-modern{
            position:relative;
        }

        .input-group-modern .bi{
            position:absolute;
            top:50%;
            left:14px;
            transform:translateY(-50%);
            color:#8e79b8;
            z-index:3;
        }

        .input-group-modern .form-control{
            padding-left:2.7rem;
        }

        .btn{
            border-radius:18px;
            font-weight:700;
            transition:.25s ease;
        }

        .btn-primary{
            background:linear-gradient(135deg, var(--app-primary) 0%, var(--app-primary-dark) 100%);
            border:none;
            box-shadow:0 10px 22px var(--app-glow);
        }

        .btn-primary:hover{
            transform:translateY(-1px);
            box-shadow:0 14px 28px rgba(155,114,207,.28);
        }

        .auth-link{
            color:var(--app-primary-dark);
            text-decoration:none;
            font-weight:600;
        }

        .auth-link:hover{
            text-decoration:underline;
        }

        .auth-tag{
            display:inline-flex;
            align-items:center;
            gap:.4rem;
            padding:.45rem .8rem;
            border-radius:999px;
            background:rgba(255,255,255,.2);
            color:#fff;
            font-weight:600;
            font-size:.82rem;
        }

        .fade-up{
            animation: fadeUp .55s ease;
        }

        @keyframes fadeUp{
            from{ opacity:0; transform:translateY(18px); }
            to{ opacity:1; transform:translateY(0); }
        }
    </style>

    @stack('styles')
</head>
<body>
<div class="auth-page">
    <div class="floating-shape shape-1"></div>
    <div class="floating-shape shape-2"></div>
    <div class="floating-shape shape-3"></div>

    <div class="auth-shell">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@include('sweetalert::alert')

@stack('scripts')
</body>
</html>
