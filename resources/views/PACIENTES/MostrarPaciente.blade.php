<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mostrar Paciente</title>
    </head>
    <style>
        body{
            margin: 0;
            font-family: Arial, sans-serif;
        }

        th{
            background-color: #121730;
            color: white;
            font-family: Arial, sans-serif;
            padding: 12px 15px;
            font-size: 16px;
            border-bottom: 3px solid #2B3A78;
        }

        .container {
            width: calc(100% - 240px);
            min-height: 100vh;
            margin-left: 240px;
            padding: 40px;
            box-sizing: border-box;
        }

        .sidebar {
            width: 240px;
            height: 100vh;
            background-color: #304766;
            position: fixed;
            top: 0;
            left: 0;
            padding: 16px 12px;
            box-sizing: border-box;
            border-right: 1px solid #043f75;
        }

        .sidebar a {
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

        .sidebar a:hover {
            background-color: #7C99D9;
        }

        .sidebar a.active {
            background-color: #03162E;
            color: #ffffff;
        }

        td{
            background-color: #ACB7FA;
            padding: 12px 15px;
            font-size: 14px;
            border-bottom: 3px solid #2B3A78;
            text-align: center;
        }
    </style>
    <body>
    <nav class="sidebar">
        <a href="{{ route('menu-principal') }}">Inicio</a>
        <div style="margin-top: 20px;">
            <a href="{{ route('menu-principal') }}" style="border-radius: 0;">Pacientes</a>
        </div>
    </nav>
        <div class="container">
        <table>
            <tr>
                <th>Codigo</th>
                <th>Nombre</th>
                <th>Direccion</th>
                <th>Telefono</th>
                <th>Fecha de Nacimiento</th>
                <th>Sexo</th>
                <th>Edad</th>
                <th>Estatura</th>
            </tr>
            @foreach ($pacientes as $paciente)
                <tr>
                    <td>{{ $paciente->codigo }}</td>
                    <td>{{ $paciente->nombre }}</td>
                    <td>{{ $paciente->direccion }}</td>
                    <td>{{ $paciente->telefono }}</td>
                    <td>{{ $paciente->fecha_nac }}</td>
                    <td>{{ $paciente->sexo }}</td>
                    <td>{{ $paciente->salario }}</td>
                    <td>{{ $paciente->turno }}</td>
                </tr>
            @endforeach
        </table>
        </div>
    </body>
</html>