<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar medicamento | Núcleo de Diagnóstico</title>
    <link rel="stylesheet" href="{{ asset('management.css') }}">
</head>
<body>
    <main class="container">
        <header class="page-header">
            <div>
                <p class="eyebrow">Núcleo de Diagnóstico · San Miguel</p>
                <h1>Registrar medicamento</h1>
                <p class="page-description">Captura los datos del medicamento para agregarlo al catálogo.</p>
            </div>
            @include('components.management-actions')
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
            @if (session('success'))
                <div class="success-banner" role="status">{{ session('success') }}</div>
            @endif

            <form action="{{ route('medicamentos.store') }}" method="POST">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label for="codigo">Código</label>
                        <input type="number" id="codigo" name="codigo" min="1" step="1" value="{{ old('codigo') }}" required>
                        @error('codigo') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="nombre_med">Nombre del medicamento</label>
                        <input type="text" id="nombre_med" name="nombre_med" maxlength="255" value="{{ old('nombre_med') }}" required>
                        @error('nombre_med') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="via_admin">Vía de administración</label>
                        <input type="text" id="via_admin" name="via_admin" maxlength="255" value="{{ old('via_admin') }}" required>
                        @error('via_admin') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="presentacion">Presentación</label>
                        <input type="text" id="presentacion" name="presentacion" maxlength="255" value="{{ old('presentacion') }}" required>
                        @error('presentacion') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="fecha_cad">Fecha de caducidad</label>
                        <input type="date" id="fecha_cad" name="fecha_cad" value="{{ old('fecha_cad') }}" required>
                        @error('fecha_cad') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="button-group">
                    <button type="submit" class="button button-primary">Guardar medicamento</button>
                    <a href="{{ route('medicamentos.index') }}" class="button button-secondary">Cancelar</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
