<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Medicamento | San Miguel</title>
    <link rel="stylesheet" href="{{ asset('management.css') }}">
</head>
<body>
    <nav class="sidebar" aria-label="Navegación principal">
        <a href="{{ route('menu-principal') }}">Inicio</a>
        <div class="sidebar-item">
            <a href="{{ route('empleados.index') }}" class="active">Empleados</a>
            <div class="submenu">
                @can('gestionar-altas') <a href="{{ route('empleados.create') }}" class="active">Agregar empleados</a> @endcan
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
            <a href="{{ route('pacientes.index') }}">Pacientes</a>
            <div class="submenu">
                <a href="{{ route('pacientes.create') }}">Agregar paciente</a>
                <a href="{{ route('pacientes.index') }}">Consultas generales</a>
            </div>
        </div>
        <div class="sidebar-item">
            <a href="{{ route('citas.index') }}">Citas</a>
            <div class="submenu">
                <a href="{{ route('citas.create') }}">Agregar cita</a>
                <a href="{{ route('citas.index') }}">Consultas generales</a>
            </div>
        </div>
        <div class="sidebar-item">
            <a href="{{ route('medicamentos.index') }}">Medicamentos</a>
            <div class="submenu">
                                @can('gestionar-altas') <a href="{{ route('medicamentos.create') }}">Agregar medicamento</a> @endcan
                <a href="{{ route('medicamentos.index') }}">Consultas generales</a>
            </div>
        </div>
    </nav>

    <main class="container">
        <header class="page-header">
            <div>
                <p class="eyebrow">Núcleo de Diagnóstico · San Miguel</p>
                <h1>Agregar Medicamento</h1>
                <p class="page-description">Completa los datos del medicamento.</p>
            </div>
        </header>

        <section class="panel">
            @if ($errors->any())
                <div class="alert" role="alert">
                    <strong>Revisa la información del formulario:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('medicamentos.store') }}" method="POST">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label for="codigo">Código</label>
                        <input type="number" id="codigo" name="codigo" value="{{ old('codigo') }}" required>
                        @error('codigo') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="nombre">Nombre del medicamento</label>
                        <input type="character varying" id="nombre_med" name="nombre_med" value="{{ old('nombre_med') }}" required>
                        @error('nombre_med') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="via_admin">Via de administracion</label>
                        <input type="character varying" id="via_admin" name="via_admin" value="{{ old('via_admin') }}" required>
                        @error('via_admin') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="telefono">Presentacion</label>
                        <input type="character varying" id="presentacion" name="presentacion" value="{{ old('presentacion') }}" required>
                        @error('presentacion') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="fecha_nac">Fecha de caducidad</label>
                        <input type="date" id="fecha_cad" name="fecha_cad" value="{{ old('fecha_cad') }}" required>
                        @error('fecha_cad') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="button-group">
                    <button type="submit" class="button button-primary">Guardar medicamento</button>
                    <a href="{{ route('menu-principal') }}" class="button button-secondary">Cancelar</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>