<div class="page-actions">
    <a href="{{ route('menu-principal') }}" class="button button-secondary">Volver al Menú Principal</a>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="button button-secondary">Cerrar Sesión</button>
    </form>
</div>
