<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú principal | Núcleo de Diagnóstico San Mariscal</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Inter, "Segoe UI", Arial, sans-serif;
            font-synthesis: none;
            text-rendering: optimizeLegibility;
            --navy: #29475d;
            --sidebar: #3b5b73;
            --blue: #4b879f;
            --light-blue: #a9cad3;
        }

        * {
            box-sizing: border-box;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            color: var(--navy);
            background:
                radial-gradient(ellipse at 82% 18%, rgba(171, 207, 220, .38), transparent 36%),
                radial-gradient(ellipse at 64% 88%, rgba(183, 216, 205, .28), transparent 38%),
                linear-gradient(135deg, #fbfdfe 0%, #eff6f8 50%, #e8f1f3 100%);
        }

        body::before,
        body::after {
            position: fixed;
            z-index: 0;
            width: 340px;
            height: 340px;
            border: 1px solid rgba(75, 135, 159, .14);
            border-radius: 50%;
            content: "";
            pointer-events: none;
        }

        body::before {
            top: -190px;
            right: 12%;
        }

        body::after {
            right: -210px;
            bottom: -180px;
            width: 440px;
            height: 440px;
        }

        .sidebar {
            position: fixed;
            z-index: 2;
            order: 1;
            top: 0;
            left: 0;
            display: flex;
            width: 260px;
            height: 100vh;
            flex-direction: column;
            gap: 8px;
            padding: 22px 14px 16px;
            border-right: 1px solid rgba(255, 255, 255, .12);
            background: linear-gradient(165deg, #496a80 0%, #3b5b73 100%);
            box-shadow: 12px 0 34px rgba(41, 71, 93, .1);
        }

        .sidebar a,
        .sidebar button {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            border: 1px solid transparent;
            border-radius: 14px;
            color: rgba(255, 255, 255, .9);
            font: inherit;
            font-size: 15px;
            font-weight: 600;
            text-align: left;
            text-decoration: none;
            transition: background-color .18s ease, border-color .18s ease, transform .18s ease;
        }

        .sidebar a:hover,
        .sidebar button:hover {
            transform: translateX(3px);
            border-color: rgba(255, 255, 255, .12);
            background-color: rgba(255, 255, 255, .12);
        }

        .sidebar a:focus-visible,
        .sidebar button:focus-visible {
            outline: 3px solid #a9d8f0;
            outline-offset: 2px;
        }

        .sidebar a.active {
            border-color: rgba(255, 255, 255, .16);
            background: linear-gradient(120deg, #659bb0, #4b879f);
            color: #fff;
            box-shadow: 0 5px 14px rgba(34, 69, 89, .14);
        }

        .sidebar-item {
            display: flex;
            flex-direction: column;
        }

        .submenu {
            display: none;
            flex-direction: column;
            gap: 3px;
            padding: 3px 0 5px 13px;
        }

        .sidebar-item:hover .submenu,
        .sidebar-item:focus-within .submenu {
            display: flex;
        }

        .sidebar .submenu a {
            padding: 9px 12px;
            color: rgba(255, 255, 255, .78);
            font-size: 13px;
            font-weight: 500;
        }

        .sidebar form {
            margin: 2px 0 0;
        }

        .sidebar button {
            width: 100%;
            background: transparent;
            cursor: pointer;
        }

        .sidebar-user {
            padding: 0 12px 14px;
            border-bottom: 1px solid rgba(255, 255, 255, .2);
            color: rgba(255, 255, 255, .72);
            font-size: 13px;
        }

        .sidebar-user strong {
            display: block;
            margin-top: 5px;
            color: #fff;
            font-size: 14px;
            overflow-wrap: anywhere;
        }

        .main-content {
            position: relative;
            z-index: 1;
            display: grid;
            order: 2;
            width: calc(100% - 260px);
            min-height: 100vh;
            margin-left: 260px;
            overflow: hidden;
            padding: clamp(28px, 5vw, 76px);
            place-items: center;
        }

        .main-content::before,
        .main-content::after {
            position: absolute;
            border: 1px solid rgba(75, 135, 159, .17);
            border-radius: 50%;
            content: "";
            pointer-events: none;
        }

        .main-content::before {
            top: 8%;
            right: 3%;
            width: 260px;
            height: 260px;
            box-shadow: 0 0 0 24px rgba(75, 135, 159, .035), 0 0 0 48px rgba(75, 135, 159, .025);
        }

        .main-content::after {
            bottom: 4%;
            left: 3%;
            width: 150px;
            height: 150px;
        }

        .brand-card {
            position: relative;
            display: flex;
            width: min(100%, 1120px);
            min-height: min(680px, calc(100vh - 100px));
            align-items: center;
            gap: clamp(38px, 7vw, 92px);
            padding: clamp(26px, 5vw, 60px);
            isolation: isolate;
        }

        .brand-card::before {
            position: absolute;
            z-index: -1;
            top: 50%;
            left: -100px;
            width: 460px;
            height: 460px;
            transform: translateY(-50%);
            border: 1px solid rgba(75, 135, 159, .11);
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, .48), rgba(226, 240, 243, .16) 65%, transparent 66%);
            box-shadow: 0 0 0 20px rgba(75, 135, 159, .025), 0 0 0 44px rgba(75, 135, 159, .02);
            content: "";
            pointer-events: none;
        }

        .brand-card::after {
            position: absolute;
            z-index: -1;
            top: 4%;
            right: -160px;
            width: 420px;
            height: 420px;
            border: 1px solid rgba(75, 135, 159, .13);
            border-radius: 50%;
            box-shadow: 0 0 0 26px rgba(75, 135, 159, .03), 0 0 0 54px rgba(75, 135, 159, .02);
            content: "";
            pointer-events: none;
        }

        .brand-logo-frame {
            position: relative;
            display: grid;
            width: clamp(180px, 25vw, 250px);
            aspect-ratio: 1;
            flex: 0 0 auto;
            padding: 7px;
            border: 1px solid rgba(75, 135, 159, .15);
            border-radius: 50%;
            background: rgba(255, 255, 255, .62);
            box-shadow: 0 0 0 12px rgba(255, 255, 255, .32), 0 18px 42px rgba(51, 91, 113, .1);
            place-items: center;
        }

        .brand-logo {
            display: block;
            width: 100%;
            height: auto;
            border-radius: 50%;
            mix-blend-mode: multiply;
        }

        .brand-badge {
            position: absolute;
            right: 2px;
            bottom: 24px;
            display: grid;
            width: 44px;
            height: 44px;
            border: 3px solid #eff6f8;
            border-radius: 50%;
            background: #d7eee7;
            color: #4b879f;
            font-size: 26px;
            font-weight: 500;
            line-height: 1;
            box-shadow: 0 5px 14px rgba(51, 91, 113, .12);
            place-items: center;
        }

        .brand-copy {
            position: relative;
            z-index: 1;
            max-width: 430px;
        }

        .brand-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            margin: 0 0 22px;
            padding: 8px 13px;
            border: 1px solid rgba(75, 135, 159, .16);
            border-radius: 999px;
            background: rgba(255, 255, 255, .56);
            color: #547a8e;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .brand-eyebrow::before {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #78b9a2;
            box-shadow: 0 0 0 4px rgba(120, 185, 162, .14);
            content: "";
        }

        .brand-copy h1 {
            margin: 0;
            color: #405f73;
            font-size: clamp(22px, 2.8vw, 30px);
            font-weight: 600;
            letter-spacing: .015em;
        }

        .brand-name {
            margin: 8px 0 0;
            color: #397791;
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(46px, 6.4vw, 74px);
            font-weight: 700;
            letter-spacing: -.045em;
            line-height: 1.02;
        }

        .brand-divider {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 21px;
        }

        .brand-divider span {
            width: 54px;
            height: 3px;
            border-radius: 99px;
            background: #79b4aa;
        }

        .brand-divider span:nth-child(2) {
            width: 22px;
            opacity: .7;
        }

        .brand-divider span:nth-child(3) {
            width: 8px;
            opacity: .45;
        }

        .brand-copy p {
            margin: 17px 0 0;
            color: #668092;
            font-size: 16px;
            line-height: 1.7;
        }

        .brand-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 9px;
            margin-top: 25px;
        }

        .brand-tags span {
            padding: 8px 13px;
            border: 1px solid rgba(75, 135, 159, .13);
            border-radius: 999px;
            background: rgba(255, 255, 255, .56);
            color: #55778b;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .025em;
        }

        @media (max-width: 760px) {
            .sidebar {
                position: sticky;
                z-index: 2;
                width: 100%;
                height: auto;
                flex-direction: row;
                flex-wrap: wrap;
                padding: 10px;
            }

            .sidebar > a,
            .sidebar > form,
            .sidebar-item {
                flex: 1 1 145px;
            }

            .sidebar-user {
                display: none;
            }

            .submenu {
                display: flex;
            }

            .main-content {
                width: 100%;
                min-height: calc(100vh - 90px);
                margin-left: 0;
                padding: 20px 16px;
            }

            .brand-card {
                flex-direction: column;
                gap: 30px;
                min-height: auto;
                padding: 48px 12px 40px;
                text-align: center;
            }

            .brand-card::before {
                top: 7px;
                left: 50%;
                width: min(340px, 88vw);
                height: min(340px, 88vw);
                transform: translateX(-50%);
            }

            .brand-logo {
                width: 100%;
            }

            .brand-logo-frame {
                width: min(190px, 58vw);
            }

            .brand-copy {
                max-width: 500px;
            }

            .brand-eyebrow {
                margin-bottom: 16px;
            }

            .brand-name {
                font-size: clamp(44px, 12vw, 62px);
            }

            .brand-divider {
                justify-content: center;
                margin-top: 18px;
            }

            .brand-copy p {
                margin-top: 12px;
            }

            .brand-tags {
                justify-content: center;
                margin-top: 20px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>
<body>
    <main class="main-content">
        <section class="brand-card" aria-labelledby="brand-title">
            <div class="brand-logo-frame">
                <span class="brand-badge" aria-hidden="true">+</span>
                <img class="brand-logo" src="{{ asset('NDBackground.png') }}" alt="Logo de Núcleo de Diagnóstico">
            </div>
            <div class="brand-copy">
                <p class="brand-eyebrow">Centro de atención y diagnóstico</p>
                <h1 id="brand-title">Núcleo de Diagnóstico</h1>
                <h2 class="brand-name">San Mariscal</h2>
                <div class="brand-divider" aria-hidden="true"><span></span><span></span><span></span></div>
                <p>Atención cercana, diagnóstico y cuidado para nuestra comunidad.</p>
                <div class="brand-tags" aria-label="Servicios">
                    <span>Atención</span>
                    <span>Diagnóstico</span>
                    <span>Bienestar</span>
                </div>
            </div>
        </section>
    </main>

    <nav class="sidebar" aria-label="Navegación principal">
        <div class="sidebar-user">
            <span>Hola, <strong>{{ explode(' ', Auth::user()->name)[0] }}</strong></span>
        </div>
        <a href="{{ route('menu-principal') }}" class="active">Inicio</a>
        <div class="sidebar-item">
            <a href="{{ route('empleados.index') }}">Empleados</a>
            <div class="submenu">
                @can('gestionar-altas')
                    <a href="{{ route('empleados.create') }}">Agregar Empleados</a>
                @endcan
                <a href="{{ route('empleados.index') }}">Consultas generales</a>
            </div>
        </div>
        <div class="sidebar-item">
            <a href="{{ route('doctores.index') }}">Doctores</a>
            <div class="submenu">
                @can('gestionar-altas')
                    <a href="{{ route('doctores.create') }}">Agregar Doctores</a>
                @endcan
                <a href="{{ route('doctores.index') }}">Consultas generales</a>
            </div>
        </div>
        <div class="sidebar-item">
            <a href="{{ route('citas.create') }}">Citas</a>
            <div class="submenu">
                <a href="{{ route('citas.create') }}">Registrar cita</a>
                <a href="{{ route('citas.create') }}">Ver agenda</a>
            </div>
        </div>
        <div class="sidebar-item">
            <a href="{{ route('pacientes.index') }}">Pacientes</a>
            <div class="submenu">
                <a href="{{ route('pacientes.create') }}">Agregar Paciente</a>
                <a href="{{ route('pacientes.index') }}">Consultas generales</a>
            </div>
        </div>
        <a href="#Perfil">Perfil</a>
        <a href="#Configuración">Configuración</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-logout">Cerrar sesión</button>
        </form>
    </nav>
</body>
</html>
