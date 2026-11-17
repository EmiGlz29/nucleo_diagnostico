<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Principal</title>
</head>
<style>
        body{
            background-image: url('{{ asset('NDBackground.png') }}');
            background-position: calc(50% + 120px) center;
            background-repeat: no-repeat; /* Evitar repeticion */
            background-size: cover; /* Ajustar tamaño de la imagen al contenedor */
            background-attachment: fixed; /* Fijar la imagen al fondo */
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .main-content{
            width: calc(100% - 240px);
            min-height: 100vh;
            margin-left: 240px;
            padding-top: 40px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1{
            margin: 0;
            color: black; /* Color del texto */
            text-align: center; /* Centrar el texto */
        }

        h2{
            margin-top: 5px;
            color: black;
            text-align: center;
        }

        h3{
            position: fixed;
            left: 20px;
            bottom: 20px;
            margin: 0;
        }

        .sidebar{
            width: 240px;
            height: 100vh;
            background-color: #304766;
            position: fixed;
            top: 0;
            left: 0;
            padding: 16px 12px;
            box-sizing: border-box;
            border-right: 1px solid #043f75;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .sidebar a{
            text-decoration: none;
            color: #121629;
            padding: 12px 16px;
            border-radius: 24px;
            font-size: 16px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: background-color 0.2s;
        }

        .sidebar a:hover{
            background-color: #7C99D9;
        }

        .sidebar a.active{
            background-color: #03162E;
            color: #ffffff; /* Cambiar el color del texto cuando está activo */
        }

        .sidebar-user{
            margin-top: auto;
            padding: 12px 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.3);
            color: #ffffff;
            font-size: 14px;
        }

        .sidebar-user strong{
            display: block;
            margin-top: 4px;
            overflow-wrap: anywhere;
        }

        .submenu{
            display: none;
            flex-direction: column;
            padding-left: 16px;
        }

        .sidebar-item{
            display: flex;
            flex-direction: column;
        }

        .sidebar-item:hover .submenu {
            display: flex;
        }

    </style>
<body>
    <main class="main-content">
        <h1>Menu Principal</h1>
        <h2>Nucleo de diagnostico</h2>
    </main>
    <nav class="sidebar">
        <a href="{{ route('menu-principal') }}" class="active">Inicio</a>
        <div class="sidebar-item">
            <a href="{{ route('empleados.create') }}">Empleados</a>
            <div class="submenu">
                <a href="{{ route('empleados.create') }}">Agregar Empleados</a>
                <a href="{{ route('empleados.index') }}">Consultas Generales</a>
            </div>
        </div>
        <a href="#Perfil">Perfil</a>
        <a href="#Configuración">Configuración</a>
        <a href="#CerrarSesion">Cerrar sesión</a>
        <div class="sidebar-user">
            <span>Conectado como:</span>
            <strong>{{ Auth::user()->name }}</strong>
        </div>
    </nav>
</body>
</html>