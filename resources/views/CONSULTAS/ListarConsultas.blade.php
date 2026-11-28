<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultas | Núcleo de Diagnóstico</title>
    <link rel="stylesheet" href="{{ asset('management.css') }}">
</head>
<body>
    <nav class="sidebar" aria-label="Navegación principal">
        <div class="sidebar-user">
            Hola, <strong>{{ explode(' ', Auth::user()->name)[0] }}</strong>
        </div>
        <a href="{{ route('menu-principal') }}">Inicio</a>
        <div class="sidebar-item">
            <a href="{{ route('empleados.index') }}">Empleados</a>
            <div class="submenu">
                @can('gestionar-altas')
                    <a href="{{ route('empleados.create') }}">Agregar empleados</a>
                @endcan
                <a href="{{ route('empleados.index') }}">Consultas generales</a>
            </div>
        </div>
        <div class="sidebar-item">
            <a href="{{ route('doctores.index') }}">Doctores</a>
            <div class="submenu">
                @can('gestionar-altas')
                    <a href="{{ route('doctores.create') }}">Agregar doctores</a>
                @endcan
                <a href="{{ route('doctores.index') }}">Consultas generales</a>
            </div>
        </div>
        <div class="sidebar-item">
            <a href="{{ route('pacientes.index') }}">Pacientes</a>
            <div class="submenu">
                <a href="{{ route('pacientes.create') }}">Agregar paciente</a>
                <a href="{{ route('pacientes.index') }}">Consultas generales</a>
            </div>
        </div>
        <div class="sidebar-item">
            <a href="{{ route('citas.create') }}">Citas</a>
            <div class="submenu">
                <a href="{{ route('citas.create') }}">Registrar cita</a>
                <a href="{{ route('citas.index') }}">Ver citas</a>
            </div>
        </div>
        <div class="sidebar-item">
            <a href="{{ route('consultas.index') }}" class="active">Consultas</a>
            <div class="submenu">
                <a href="{{ route('consultas.index') }}" class="active">Ver consultas</a>
            </div>
        </div>
        <div class="sidebar-item">
            <a href="{{ route('medicamentos.index') }}">Medicamentos</a>
            <div class="submenu">
                @can('gestionar-altas')
                    <a href="{{ route('medicamentos.create') }}">Agregar medicamento</a>
                @endcan
                <a href="{{ route('medicamentos.index') }}">Consultas generales</a>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-logout">Cerrar sesión</button>
        </form>
    </nav>

    <main class="container">
        <header class="page-header">
            <div>
                <p class="eyebrow">Atención médica</p>
                <h1>Consultas registradas</h1>
                <p class="page-description">Historial de todas las consultas médicas guardadas en el sistema.</p>
            </div>
            @include('components.management-actions')
        </header>

        @if ($consultas->isEmpty())
            <section class="panel">
                <p class="empty-state">No hay consultas registradas.</p>
            </section>
        @else
            <section class="panel">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Paciente</th>
                                <th>Doctor</th>
                                <th>Cita</th>
                                <th>Medicamento</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($consultas as $consulta)
                                <tr>
                                    <td>#{{ $consulta->codigo }}</td>
                                    <td>{{ $consulta->paciente->nombre }}</td>
                                    <td>{{ $consulta->doctor->nombre }}</td>
                                    <td>#{{ $consulta->codigo_cita }}</td>
                                    <td>{{ $consulta->medicamento->nombre_med }}</td>
                                    <td>
                                        <a href="{{ route('consultas.show', $consulta) }}" class="button button-small">Ver</a>
                                        <a href="{{ route('consultas.receta', $consulta) }}" class="button button-small">Descargar receta</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif
    </main>
</body>
</html>
