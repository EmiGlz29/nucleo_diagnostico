<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicamentos | San Miguel</title>
    <link rel="stylesheet" href="{{ asset('management.css') }}">
</head>
<body>
    <nav class="sidebar" aria-label="Navegación principal">
        <a href="{{ route('menu-principal') }}">Inicio</a>
        <div class="sidebar-item">
            <a href="{{ route('empleados.index') }}">Empleados</a>
            <div class="submenu">
                                @can('gestionar-altas') <a href="{{ route('empleados.create') }}">Agregar empleados</a> @endcan
                <a href="{{ route('empleados.index') }}" class="active">Consultas generales</a>
            </div>
        </div>
        <div class="sidebar-item">
            <a href="{{ route('medicamentos.index') }}" class="active">Medicamentos</a>
            <div class="submenu">
                @can('gestionar-altas') <a href="{{ route('medicamentos.create') }}">Agregar medicamento</a> @endcan
                <a href="{{ route('medicamentos.index') }}" class="active">Consultas generales</a>
            </div>
        </div>
        <div class="sidebar-item">
            <a href="{{ route('doctores.index') }}">Doctores</a>
            <div class="submenu">
                @can('gestionar-altas') <a href="{{ route('doctores.create') }}">Agregar doctores</a> @endcan
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
    </nav>

    <main class="container">
        <header class="page-header">
            <div>
                <p class="eyebrow">Núcleo de Diagnóstico · San Miguel</p>
                <h1>Medicamentos</h1>
                <p class="page-description">{{ $medicamentos->count() }} {{ $medicamentos->count() === 1 ? 'registro' : 'registros' }} en el directorio.</p>
            </div>
        </header>

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th scope="col">Código</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Via de administracion</th>
                        <th scope="col">Presentacion</th>
                        <th scope="col">Fecha de caducidad</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($medicamentos as $medicamento)
                        <tr>
                            <td>{{ $medicamento->codigo }}</td>
                            <td>{{ $medicamento->nombre_med }}</td>
                            <td>{{ $medicamento->via_admin }}</td>
                            <td>{{ $medicamento->presentacion }}</td>
                            <td>{{ $medicamento->fecha_cad }}</td>
                        </tr>
                    @empty
                        <tr><td class="empty-state" colspan="5">Todavía no hay Medicamentos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
