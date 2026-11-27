<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Receta médica</title>
    <style>
        @page { margin: 34px 42px; }
        body { color: #17324d; font-family: DejaVu Sans, sans-serif; font-size: 12px; line-height: 1.55; }
        .header { border-bottom: 2px solid #087fb5; margin-bottom: 22px; padding-bottom: 14px; }
        .logo { height: 88px; width: auto; }
        .brand { color: #087fb5; font-size: 20px; font-weight: bold; margin: 8px 0 0; }
        .subtitle { color: #647a90; margin: 2px 0 0; }
        h1 { color: #102b49; font-size: 22px; margin: 0 0 14px; }
        h2 { border-bottom: 1px solid #d5e0eb; color: #087fb5; font-size: 14px; margin: 20px 0 8px; padding-bottom: 4px; }
        table { border-collapse: collapse; width: 100%; }
        td { padding: 5px 7px; vertical-align: top; }
        .label { color: #647a90; font-weight: bold; width: 31%; }
        .diagnosis { background: #f3f8fc; border-left: 3px solid #087fb5; padding: 10px 12px; white-space: pre-wrap; }
        .footer { border-top: 1px solid #d5e0eb; color: #647a90; font-size: 10px; margin-top: 34px; padding-top: 10px; text-align: center; }
    </style>
</head>
<body>
    <header class="header">
        <img class="logo" src="{{ $logo }}" alt="Logo de Núcleo de Diagnóstico">
        <p class="brand">Núcleo de Diagnóstico</p>
        <p class="subtitle">San Miguel · Receta médica</p>
    </header>

    <h1>Receta médica</h1>
    <table>
        <tr><td class="label">Paciente</td><td>{{ $consulta->paciente->nombre }}</td></tr>
        <tr><td class="label">Código de paciente</td><td>{{ $consulta->paciente->codigo }}</td></tr>
        <tr><td class="label">Fecha de nacimiento</td><td>{{ \Illuminate\Support\Carbon::parse($consulta->paciente->fecha_nac)->format('d/m/Y') }}</td></tr>
        <tr><td class="label">Sexo</td><td>{{ $consulta->paciente->sexo }}</td></tr>
        <tr><td class="label">Edad</td><td>{{ $consulta->paciente->edad }} años</td></tr>
        <tr><td class="label">Teléfono</td><td>{{ $consulta->paciente->telefono }}</td></tr>
        <tr><td class="label">Dirección</td><td>{{ $consulta->paciente->direccion }}</td></tr>
        <tr><td class="label">Estatura</td><td>{{ $consulta->paciente->estatura }}</td></tr>
    </table>

    <h2>Diagnóstico e indicaciones</h2>
    <div class="diagnosis">{{ $consulta->diagnostico }}</div>

    <h2>Prescripción</h2>
    <table>
        <tr><td class="label">Medicamento</td><td>{{ $consulta->medicamento->nombre_med }}</td></tr>
        <tr><td class="label">Vía de administración</td><td>{{ $consulta->medicamento->via_admin }}</td></tr>
        <tr><td class="label">Presentación</td><td>{{ $consulta->medicamento->presentacion }}</td></tr>
        <tr><td class="label">Código del medicamento</td><td>{{ $consulta->medicamento->codigo }}</td></tr>
        <tr><td class="label">Caducidad</td><td>{{ \Illuminate\Support\Carbon::parse($consulta->medicamento->fecha_cad)->format('d/m/Y') }}</td></tr>
        <tr><td class="label">Días de tratamiento</td><td>{{ $consulta->dias_tratamiento }}</td></tr>
        <tr><td class="label">Horario de suministro</td><td>{{ $consulta->horario_suministro }}</td></tr>
    </table>

    <h2>Profesional que prescribe</h2>
    <table>
        <tr><td class="label">Doctor</td><td>{{ $consulta->doctor->nombre }}</td></tr>
        <tr><td class="label">Cédula / código</td><td>{{ $consulta->doctor->codigo }}</td></tr>
        <tr><td class="label">Especialidad</td><td>{{ $consulta->doctor->especialidad }}</td></tr>
        <tr><td class="label">Fecha de consulta</td><td>{{ $consulta->fecha_consulta->format('d/m/Y H:i') }}</td></tr>
        <tr><td class="label">Cita</td><td>#{{ $cita->codigocita }} · {{ $cita->fechacita }} · {{ substr((string) $cita->hora, 0, 5) }}</td></tr>
    </table>

    <footer class="footer">Documento generado por Núcleo de Diagnóstico · San Miguel</footer>
</body>
</html>
