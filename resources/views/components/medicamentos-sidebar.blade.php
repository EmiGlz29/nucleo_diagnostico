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
        <a href="{{ route('doctores.index') }}">Doctores</a>
        <div class="submenu">
            @can('gestionar-altas') <a href="{{ route('doctores.create') }}">Agregar doctores</a> @endcan
            <a href="{{ route('doctores.index') }}">Consultas generales</a>
            <a href="{{ route('citas.create') }}">Ver agenda</a>
        </div>
    </div>
    <div class="sidebar-item">
        <a href="{{ route('citas.create') }}">Citas</a>
        <div class="submenu">
            <a href="{{ route('citas.create') }}">Registrar cita</a>
            <a href="{{ route('citas.index') }}">Ver agenda</a>
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
        <a href="{{ route('medicamentos.index') }}" class="active">Medicamentos</a>
        <div class="submenu">
            @can('gestionar-altas')
                <a href="{{ route('medicamentos.create') }}" @class(['active' => request()->routeIs('medicamentos.create')])>Agregar medicamento</a>
            @endcan
            <a href="{{ route('medicamentos.index') }}" @class(['active' => request()->routeIs('medicamentos.index')])>Consultas generales</a>
        </div>
    </div>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="sidebar-logout">Cerrar sesión</button>
    </form>
</nav>
