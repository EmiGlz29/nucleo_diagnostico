<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicamentos | Núcleo de Diagnóstico</title>
    <link rel="stylesheet" href="{{ asset('management.css') }}">
</head>
<body>
    <main class="container">
        <header class="page-header">
            <div>
                <p class="eyebrow">Núcleo de Diagnóstico · San Miguel</p>
                <h1>Medicamentos</h1>
                <p class="page-description">{{ $medicamentos->count() }} {{ $medicamentos->count() === 1 ? 'registro' : 'registros' }} en el catálogo.</p>
            </div>
            <div class="page-actions">
                @can('gestionar-altas')
                    <a href="{{ route('medicamentos.create') }}" class="button button-primary">Registrar medicamento</a>
                @endcan
                @include('components.management-actions')
            </div>
        </header>

        @if (session('success'))
            <div class="success-banner" role="status">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th scope="col">Código</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Vía de administración</th>
                        <th scope="col">Presentación</th>
                        <th scope="col">Fecha de caducidad</th>
                        @can('gestionar-altas') <th scope="col">Acciones</th> @endcan
                    </tr>
                </thead>
                <tbody>
                    @forelse ($medicamentos as $medicamento)
                        <tr>
                            <td>{{ $medicamento->codigo }}</td>
                            <td>{{ $medicamento->nombre_med }}</td>
                            <td>{{ $medicamento->via_admin }}</td>
                            <td>{{ $medicamento->presentacion }}</td>
                            <td>{{ \Illuminate\Support\Carbon::parse($medicamento->fecha_cad)->format('d/m/Y') }}</td>
                            @can('gestionar-altas')
                                <td><a href="{{ route('medicamentos.edit', $medicamento) }}" class="button button-secondary">Editar</a></td>
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td class="empty-state" colspan="@can('gestionar-altas') 6 @else 5 @endcan">
                                Todavía no hay medicamentos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
