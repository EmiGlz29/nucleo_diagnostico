<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar empleado | San Miguel</title>
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
    </nav>

    <main class="container">
        <header class="page-header">
            <div>
                <p class="eyebrow">Núcleo de Diagnóstico · San Miguel</p>
                <h1>Agregar empleado</h1>
                <p class="page-description">Completa los datos personales y laborales del empleado.</p>
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

            <form action="{{ route('empleados.store') }}" method="POST">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label for="codigo">Código</label>
                        <input type="number" id="codigo" name="codigo" value="{{ old('codigo') }}" required>
                        @error('codigo') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="nombre">Nombre completo</label>
                        <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                        @error('nombre') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="direccion">Dirección</label>
                        <input type="text" id="direccion" name="direccion" value="{{ old('direccion') }}" required>
                        @error('direccion') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono" value="{{ old('telefono') }}" required>
                        @error('telefono') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="fecha_nac">Fecha de nacimiento</label>
                        <input type="date" id="fecha_nac" name="fecha_nac" value="{{ old('fecha_nac') }}" required>
                        @error('fecha_nac') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="sexo">Sexo</label>
                        <input type="text" id="sexo" name="sexo" value="{{ old('sexo') }}" required>
                        @error('sexo') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="salario">Salario</label>
                        <input type="number" step="0.01" min="0" id="salario" name="salario" value="{{ old('salario') }}" required>
                        @error('salario') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="turno">Turno</label>
                        <input type="text" id="turno" name="turno" value="{{ old('turno') }}" required>
                        @error('turno') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="contraseña">Contraseña</label>
                        <input type="password" id="contraseña" name="contraseña" autocomplete="new-password" required>
                        @error('contraseña') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="button-group">
                    <button type="submit" class="button button-primary">Guardar empleado</button>
                    <a href="{{ route('menu-principal') }}" class="button button-secondary">Cancelar</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
