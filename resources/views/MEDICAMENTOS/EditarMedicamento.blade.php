<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar medicamento | Núcleo de Diagnóstico</title>
    <link rel="stylesheet" href="{{ asset('management.css') }}">
</head>
<body>
    <main class="container">
        <header class="page-header">
            <div>
                <p class="eyebrow">Núcleo de Diagnóstico · San Miguel</p>
                <h1>Editar medicamento</h1>
                <p class="page-description">Actualiza la información del medicamento seleccionado.</p>
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
            <form action="{{ route('medicamentos.update', $medicamento) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-grid">
                    <div class="form-group">
                        <label for="codigo">Código</label>
                        <input type="number" id="codigo" name="codigo" min="1" step="1" value="{{ old('codigo', $medicamento->codigo) }}" required>
                        @error('codigo') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="nombre_med">Nombre del medicamento</label>
                        <input type="text" id="nombre_med" name="nombre_med" maxlength="255" value="{{ old('nombre_med', $medicamento->nombre_med) }}" required>
                        @error('nombre_med') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="via_admin">Vía de administración</label>
                        <input type="text" id="via_admin" name="via_admin" maxlength="255" value="{{ old('via_admin', $medicamento->via_admin) }}" required>
                        @error('via_admin') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="presentacion">Presentación</label>
                        <input type="text" id="presentacion" name="presentacion" maxlength="255" value="{{ old('presentacion', $medicamento->presentacion) }}" required>
                        @error('presentacion') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="fecha_cad">Fecha de caducidad</label>
                        <input type="date" id="fecha_cad" name="fecha_cad" value="{{ old('fecha_cad', \Illuminate\Support\Carbon::parse($medicamento->fecha_cad)->format('Y-m-d')) }}" required>
                        @error('fecha_cad') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="button-group">
                    <button type="submit" class="button button-primary">Guardar cambios</button>
                    <a href="{{ route('medicamentos.index') }}" class="button button-secondary">Cancelar</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
