<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda del doctor {{ $doctor->nombre }} | Núcleo de Diagnóstico</title>
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
                <p class="eyebrow">Núcleo de Diagnóstico</p>
                <h1>Agenda del doctor {{ $doctor->nombre }}</h1>
            </div>
            <div class="page-actions">
                <a class="button button-secondary" href="{{ route('doctores.index') }}">Volver a doctores</a>
                @include('components.management-actions')
            </div>
        </header>

        <nav class="button-group agenda-navigation" aria-label="Cambiar semana">
            <a class="button button-secondary" href="{{ route('doctores.agenda', ['doctor' => $doctor, 'semana' => $semanaAnterior]) }}">« Semana anterior</a>
            <a class="button button-secondary" href="{{ route('doctores.agenda', ['doctor' => $doctor, 'semana' => $semanaSiguiente]) }}">Semana siguiente »</a>
        </nav>

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th scope="col">Horas</th>
                        @foreach ($dias as $dia)
                            <th scope="col">
                                {{ ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'][$dia->dayOfWeekIso] }}
                                <br>
                                {{ $dia->format('d/m') }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($horas as $hora)
                        <tr>
                            <td>{{ $hora['inicio'] }} A {{ $hora['fin'] }}</td>
                            @foreach ($dias as $dia)
                                @php
                                    $cita = $citas->get($dia->toDateString().'|'.$hora['inicio']);
                                @endphp
                                <td>
                                    @if ($hora['comida'])
                                        COMIDA
                                    @elseif ($cita)
                                        Paciente {{ $cita->paciente->nombre }}
                                        
                                    @else
                                        Libre
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
