<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta registrada | Núcleo de Diagnóstico</title>
    <link rel="stylesheet" href="{{ asset('management.css') }}">
</head>
<body>
    <main class="container">
        <header class="page-header">
            <div>
                <p class="eyebrow">Consulta registrada</p>
                <h1>Detalle de la consulta #{{ $consulta->codigoconsulta }}</h1>
                <p class="page-description">La consulta quedó asociada a la cita #{{ $cita->codigocita }}.</p>
            </div>
            @include('components.management-actions')
        </header>

        <section class="panel">
            <h2>Paciente</h2>
            <div class="form-grid">
                <p><strong>Nombre:</strong> {{ $consulta->paciente->nombre }}</p>
                <p><strong>Código:</strong> {{ $consulta->paciente->codigo }}</p>
                <p><strong>Fecha de nacimiento:</strong> {{ $consulta->paciente->fecha_nac }}</p>
                <p><strong>Sexo:</strong> {{ $consulta->paciente->sexo }}</p>
                <p><strong>Edad:</strong> {{ $consulta->paciente->edad }}</p>
                <p><strong>Teléfono:</strong> {{ $consulta->paciente->telefono }}</p>
                <p><strong>Dirección:</strong> {{ $consulta->paciente->direccion }}</p>
                <p><strong>Estatura:</strong> {{ $consulta->paciente->estatura }}</p>
            </div>
        </section>

        <section class="panel">
            <h2>Diagnóstico e indicaciones</h2>
            <p class="consultation-diagnosis">{{ $consulta->diagnostico }}</p>
            <div class="form-grid">
                <p><strong>Medicamento:</strong> {{ $consulta->medicamento->nombre_med }}</p>
                <p><strong>Vía:</strong> {{ $consulta->medicamento->via_admin }}</p>
                <p><strong>Presentación:</strong> {{ $consulta->medicamento->presentacion }}</p>
                <p><strong>Días:</strong> {{ $consulta->dias_tratamiento }}</p>
                <p><strong>Horario:</strong> {{ $consulta->horario_suministro }}</p>
                <p><strong>Doctor:</strong> {{ $consulta->doctor->nombre }} ({{ $consulta->doctor->codigo }})</p>
                <p><strong>Fecha de consulta:</strong> {{ $consulta->fecha_consulta->format('d/m/Y H:i') }}</p>
            </div>
            <div class="button-group">
                <a href="{{ route('consultas.receta', $consulta) }}" class="button button-primary">GENERAR RECETA</a>
                <a href="{{ route('citas.index') }}" class="button button-secondary">Volver a las citas</a>
            </div>
        </section>
    </main>
</body>
</html>
