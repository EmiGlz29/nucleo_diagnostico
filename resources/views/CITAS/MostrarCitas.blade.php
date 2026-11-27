<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Listado de Citas | San Miguel</title>
    <link rel="stylesheet" href="{{ asset('management.css') }}">
    <style>
        .header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #ddd;
        }

        .header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: #102b49;
        }

        .header p {
            color: #647a90;
            font-size: 1rem;
        }

        .table-wrapper {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f9f9f9;
            border-bottom: 2px solid #e0e0e0;
        }

        th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #102b49;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid #e0e0e0;
        }

        tr:hover {
            background: #fafafa;
        }

        .patient-cell {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .patient-name {
            font-weight: 500;
        }

        .doctor-cell{
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .doctor-name{
            font-weight: 500;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background: #087fb5;
            color: white;
        }

        .btn-primary:hover {
            background: #056a8f;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #545b62;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #999;
        }

        .empty-state p {
            margin-bottom: 1rem;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 1rem;
            color: #087fb5;
            text-decoration: none;
            font-weight: 500;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .status-label {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-scheduled {
            background: #d4edda;
            color: #155724;
        }

        .status-past {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
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
                                @can('gestionar-altas') <a href="{{ route('empleados.create') }}">Agregar empleados</a> @endcan
                <a href="{{ route('empleados.index') }}">Consultas generales</a>
            </div>
        </div>
        <div class="sidebar-item">
            <a href="{{ route('citas.create') }}">Citas</a>
            <div class="submenu">
                <a href="{{ route('citas.create') }}">Registrar cita</a>
                <a href="{{ route('citas.index') }}" class="active">Ver citas</a>
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
            <a href="{{ route('medicamentos.index') }}">Medicamentos</a>
            <div class="submenu">
                                @can('gestionar-altas') <a href="{{ route('medicamentos.create') }}">Agregar medicamento</a> @endcan
                <a href="{{ route('medicamentos.index') }}" class="active">Consultas generales</a>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-logout">Cerrar sesión</button>
        </form>
    </nav>

    <main class="container">
        <a href="{{ route('menu-principal') }}" class="back-link">← Volver al menú</a>

        <div class="header">
            <h1>Listado de Citas</h1>
            <p>Visualiza y modifica todas las citas registradas</p>
        </div>

        @if ($citas->count() > 0)
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Paciente</th>
                            <th>Doctor</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Empleado</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($citas as $cita)
                            @php
                                $isPast = \Carbon\CarbonImmutable::parse($cita->fechacita)->toDateString() < '2026-11-23';
                            @endphp
                            <tr>
                                <td><strong>#{{ $cita->codigocita }}</strong></td>
                                <td>
                                    <div class="patient-cell">
                                        <span class="patient-name">{{ $cita->paciente->nombre }}</span>
                                    </div>
                                </td>
                                <td>
                                <div class="doctor-cell">
                                    <span class="doctor-name">{{ $cita->doctor->nombre ?? '—' }}</span>
                                </div>
                                </td>
                                <td>{{ \Carbon\CarbonImmutable::parse($cita->fechacita)->format('d/m/Y') }}</td>
                                <td>{{ substr($cita->hora, 0, 5) }}</td>
                                <td>{{ $cita->empleado->nombre ?? '—' }}</td>
                                <td>
                                    <span class="status-label {{ $isPast ? 'status-past' : 'status-scheduled' }}">
                                        {{ $isPast ? 'Pasada' : 'Vigente' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('citas.edit', $cita->codigocita) }}" class="btn btn-primary">
                                            Modificar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="table-wrapper">
                <div class="empty-state">
                    <p>No hay citas registradas aún.</p>
                    <a href="{{ route('citas.create') }}" class="btn btn-primary">
                        Crear primera cita
                    </a>
                </div>
            </div>
        @endif
    </main>
</body>
</html>
