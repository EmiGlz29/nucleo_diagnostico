<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctores | Núcleo de Diagnóstico</title>
    <link rel="stylesheet" href="{{ asset('management.css') }}">
</head>
<body>
    <nav class="sidebar" aria-label="Navegación principal">
        <a href="{{ route('menu-principal') }}">Inicio</a>
        <div class="sidebar-item">
            <a href="{{ route('empleados.index') }}">Empleados</a>
            <div class="submenu">
                @can('gestionar-altas') <a href="{{ route('empleados.create') }}">Agregar empleados</a> @endcan
                <a href="{{ route('empleados.index') }}">Consultas generales</a>
            </div>
        </div>
        <div class="sidebar-item">
            <a href="{{ route('doctores.index') }}" class="active">Doctores</a>
            <div class="submenu">
                @can('gestionar-altas') <a href="{{ route('doctores.create') }}">Agregar doctores</a> @endcan
                <a href="{{ route('doctores.index') }}" class="active">Consultas generales</a>
            </div>
        </div>
        <div class="sidebar-item">
            <a href="{{ route('pacientes.index') }}">Pacientes</a>
            <div class="submenu">
                <a href="{{ route('pacientes.create') }}">Agregar paciente</a>
                <a href="{{ route('pacientes.index') }}">Consultas generales</a>
            </div>
        </div>
    </nav>

    <main class="container">
        <header class="page-header">
            <div>
                <p class="eyebrow">Núcleo de Diagnóstico · San Mariscal</p>
                <h1>Doctores</h1>
                <p class="page-description">{{ $doctores->count() }} {{ $doctores->count() === 1 ? 'registro' : 'registros' }} en el directorio.</p>
            </div>
            <a class="button button-primary" href="{{ route('citas.create') }}">Ver agenda</a>
        </header>

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th scope="col">Código</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Dirección</th>
                        <th scope="col">Teléfono</th>
                        <th scope="col">Fecha de nacimiento</th>
                        <th scope="col">Sexo</th>
                        <th scope="col">Especialidad</th>
                        <th scope="col">Agenda</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($doctores as $doctor)
                        <tr>
                            <td>{{ $doctor->codigo }}</td>
                            <td>{{ $doctor->nombre }}</td>
                            <td>{{ $doctor->direccion }}</td>
                            <td>{{ $doctor->telefono }}</td>
                            <td>{{ $doctor->fecha_nac }}</td>
                            <td>{{ $doctor->sexo }}</td>
                            <td>{{ $doctor->especialidad }}</td>
                            <td><a class="button button-secondary button-small" href="{{ route('doctores.agenda', $doctor) }}">Agenda</a></td>
                        </tr>
                    @empty
                        <tr><td class="empty-state" colspan="8">Todavía no hay doctores registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
