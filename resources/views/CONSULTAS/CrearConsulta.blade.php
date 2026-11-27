<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta médica | Núcleo de Diagnóstico</title>
    <link rel="stylesheet" href="{{ asset('management.css') }}">
</head>
<body>
    <main class="container">
        <header class="page-header">
            <div>
                <p class="eyebrow">Atención médica</p>
                <h1>Consulta médica</h1>
                <p class="page-description">Registra el diagnóstico y la prescripción para esta cita.</p>
            </div>
            @include('components.management-actions')
        </header>

        <section class="panel consultation-summary">
            <h2>Datos de la cita</h2>
            <div class="form-grid">
                <p><strong>Cita:</strong> #{{ $cita->codigocita }}</p>
                <p><strong>Fecha y hora:</strong> {{ $cita->fechacita }} · {{ substr((string) $cita->hora, 0, 5) }}</p>
                <p><strong>Doctor:</strong> {{ $cita->doctor->nombre }} ({{ $cita->doctor->codigo }})</p>
                <p><strong>Paciente:</strong> {{ $cita->paciente->nombre }} ({{ $cita->paciente->codigo }})</p>
                <p><strong>Teléfono:</strong> {{ $cita->paciente->telefono }}</p>
                <p><strong>Fecha de nacimiento:</strong> {{ $cita->paciente->fecha_nac }}</p>
                <p><strong>Sexo:</strong> {{ $cita->paciente->sexo }}</p>
                <p><strong>Edad:</strong> {{ $cita->paciente->edad }}</p>
                <p><strong>Dirección:</strong> {{ $cita->paciente->direccion }}</p>
            </div>
        </section>

        <section class="panel">
            @if ($errors->any())
                <div class="alert" role="alert">
                    <strong>No se pudo guardar la consulta:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('consultas.store') }}" method="POST">
                @csrf
                <input type="hidden" name="codigocita" value="{{ $cita->codigocita }}">
                <input type="hidden" name="codigopaciente" value="{{ $cita->codigopaciente }}">

                <div class="form-group">
                    <label for="diagnostico">Diagnóstico e historial del paciente</label>
                    <textarea id="diagnostico" name="diagnostico" rows="8" maxlength="20000" required>{{ old('diagnostico') }}</textarea>
                    @error('diagnostico') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="codigo_medicamento">Medicamento</label>
                        <select id="codigo_medicamento" name="codigo_medicamento" required>
                            <option value="">Selecciona un medicamento</option>
                            @foreach ($medicamentos as $medicamento)
                                <option value="{{ $medicamento->codigo }}" @selected((string) old('codigo_medicamento') === (string) $medicamento->codigo)>
                                    {{ $medicamento->nombre_med }} · {{ $medicamento->via_admin }} · {{ $medicamento->presentacion }}
                                </option>
                            @endforeach
                        </select>
                        @error('codigo_medicamento') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="dias_tratamiento">Días de tratamiento</label>
                        <input type="number" id="dias_tratamiento" name="dias_tratamiento" min="1" max="365" step="1" value="{{ old('dias_tratamiento') }}" required>
                        @error('dias_tratamiento') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="horario_suministro">Horario de suministro</label>
                        <input type="text" id="horario_suministro" name="horario_suministro" maxlength="255" placeholder="Ej. Cada 8 horas, con alimentos" value="{{ old('horario_suministro') }}" required>
                        @error('horario_suministro') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" class="button button-primary">Guardar consulta</button>
                    <a href="{{ route('citas.index') }}" class="button button-secondary">Volver a las citas</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
