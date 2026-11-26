<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Modificar Cita | San Miguel</title>
    <link rel="stylesheet" href="{{ asset('management.css') }}">
    <style>
        .modification-form {
            max-width: 600px;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
        }

        .button-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #545b62;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .readonly-field {
            background: #f5f5f5;
            cursor: not-allowed;
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
    </style>
</head>
<body>
    <nav class="sidebar" aria-label="Navegación principal">
        <div class="sidebar-user">
            Hola, <strong>{{ explode(' ', Auth::user()->name)[0] }}</strong>
        </div>
        <a href="{{ route('menu-principal') }}">Inicio</a>
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
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-logout">Cerrar sesión</button>
        </form>
    </nav>

    <main class="container">
        <div class="modification-form">
            <a href="{{ route('citas.index') }}" class="back-link">
                ← Volver al listado
            </a>

            <h1>Modificar Cita #{{ $cita->codigocita }}</h1>

            @if ($errors->any())
                <div style="background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                    <strong>Errores en el formulario:</strong>
                    <ul style="margin: 0.5rem 0 0 1.5rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('citas.update', $cita->codigocita) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="codigocita">Código de Cita:</label>
                    <input type="number" id="codigocita" name="codigocita" value="{{ old('codigocita', $cita->codigocita) }}" readonly class="readonly-field">
                </div>

                <div class="form-group">
                    <label for="codigopaciente">Paciente:</label>
                    <input type="text" id="codigopaciente" value="{{ $cita->paciente->nombre }} (Código: {{ $cita->codigopaciente }})" readonly class="readonly-field">
                    <input type="hidden" name="codigopaciente" value="{{ $cita->codigopaciente }}">
                </div>

                <div class="form-group">
                    <label for="codigodoctor">Doctor:</label>
                    <select id="codigodoctor" name="codigodoctor" required>
                        <option value="">Selecciona un doctor</option>
                        @foreach ($doctores as $doctor)
                            <option value="{{ $doctor->codigo }}" @selected($cita->codigodoctor === $doctor->codigo)>
                                {{ $doctor->nombre }} ({{ $doctor->codigo }})
                            </option>
                        @endforeach
                    </select>
                    @error('codigodoctor') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="fechacita">Fecha de Cita:</label>
                    <input type="date" id="fechacita" name="fechacita" value="{{ old('fechacita', $cita->fechacita) }}" required>
                    @error('fechacita') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="hora">Hora:</label>
                    <select id="hora" name="hora" required>
                        <option value="">Selecciona un horario</option>
                        @php
                            $horas = [];
                            for ($hour = 9; $hour <= 19; $hour++) {
                                // Excluir horas de comida (14:00 y 15:00)
                                if ($hour === 14 || $hour === 15) {
                                    continue;
                                }
                                $horas[] = sprintf('%02d:00', $hour);
                            }
                        @endphp
                        @foreach ($horas as $h)
                            <option value="{{ $h }}" @selected(substr($cita->hora, 0, 5) === $h)>
                                {{ $h }} - {{ sprintf('%02d:00', (int)substr($h, 0, 2) + 1) }}
                            </option>
                        @endforeach
                    </select>
                    @error('hora') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    <a href="{{ route('citas.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
