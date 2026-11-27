<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacientes | San Miguel</title>
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
            <a href="{{ route('doctores.index') }}">Doctores</a>
            <div class="submenu">
                @can('gestionar-altas') <a href="{{ route('doctores.create') }}">Agregar doctores</a> @endcan
                <a href="{{ route('doctores.index') }}">Consultas generales</a>
            </div>
        </div>
        <div class="sidebar-item">
            <a href="{{ route('pacientes.index') }}" class="active">Pacientes</a>
            <div class="submenu">
                <a href="{{ route('pacientes.create') }}">Agregar paciente</a>
                <a href="{{ route('pacientes.index') }}" class="active">Consultas generales</a>
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
                <h1>Pacientes</h1>
                <p class="page-description">{{ $pacientes->count() }} {{ $pacientes->count() === 1 ? 'registro' : 'registros' }} en el directorio.</p>
            </div>
            @include('components.management-actions')
        </header>

        @if (Auth::user() instanceof \App\Models\Doctor)
            @if ($proximaCita)
                <section class="panel">
                    <p class="eyebrow">Próxima cita · {{ $proximaCita->fechacita }} · {{ substr((string) $proximaCita->hora, 0, 5) }}</p>
                    <h2>{{ $proximaCita->paciente->nombre }}</h2>
                    <p><strong>Código:</strong> {{ $proximaCita->paciente->codigo }}</p>
                    <p><strong>Dirección:</strong> {{ $proximaCita->paciente->direccion }}</p>
                    <p><strong>Teléfono:</strong> {{ $proximaCita->paciente->telefono }}</p>
                    <p><strong>Fecha de nacimiento:</strong> {{ $proximaCita->paciente->fecha_nac }}</p>
                    <p><strong>Sexo:</strong> {{ $proximaCita->paciente->sexo }}</p>
                    <p><strong>Edad:</strong> {{ $proximaCita->paciente->edad }}</p>
                    <p><strong>Estatura:</strong> {{ $proximaCita->paciente->estatura }}</p>
                    <a class="button button-primary" href="{{ route('consultas.create', [$proximaCita->codigocita, $proximaCita->codigopaciente]) }}">Iniciar consulta</a>
                </section>
            @else
                <div class="success-banner" role="status">No tienes próximas citas asignadas.</div>
            @endif
        @endif

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
                        <th scope="col">Edad</th>
                        <th scope="col">Estatura</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pacientes as $paciente)
                        <tr>
                            <td>{{ $paciente->codigo }}</td>
                            <td>{{ $paciente->nombre }}</td>
                            <td>{{ $paciente->direccion }}</td>
                            <td>{{ $paciente->telefono }}</td>
                            <td>{{ $paciente->fecha_nac }}</td>
                            <td>{{ $paciente->sexo }}</td>
                            <td>{{ $paciente->edad }}</td>
                            <td>{{ $paciente->estatura }}</td>
                        </tr>
                    @empty
                        <tr><td class="empty-state" colspan="8">Todavía no hay pacientes registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
