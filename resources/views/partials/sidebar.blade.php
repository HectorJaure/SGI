<div class="logo mb-4 px-3">
    <h1 class="h5">Sistema de Seguridad Ocupacional</h1>
    <p class="small mb-0">ITSN - ISO 45001:2018</p>
</div>

<ul class="nav flex-column">
    <li class="nav-item">
        <a href="{{ route('dashboard') }}" class="nav-link menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-home me-2"></i> Inicio
        </a>
    </li>
    
    <!-- Mostrar gestión de usuarios solo para administradores -->
    @if(session('user_rol') === 'Administrador')
    <li class="nav-item">
        <a href="{{ route('users.index') }}" class="nav-link menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="fas fa-users me-2"></i> Usuarios
        </a>
    </li>
    @endif
    
    <li class="nav-item">
        <a href="{{ route('risks.matrix') }}" class="nav-link menu-item {{ request()->routeIs('risks.*') ? 'active' : '' }}">
            <i class="fas fa-exclamation-triangle me-2"></i> Matriz de Riesgos
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('requisitos-legales.index') }}" class="nav-link menu-item {{ request()->routeIs('requisitos-legales.*') ? 'active' : '' }}">
            <i class="fas fa-gavel me-2"></i> Matriz de Requisitos Legales
        </a>
    </li>
    
    <!-- Mostrar notificaciones solo para administradores -->
    @if(session('user_rol') === 'Administrador')
    <li class="nav-item">
        <a href="{{ route('notifications.index') }}" class="nav-link menu-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            <i class="fas fa-bell me-2"></i> Notificaciones
        </a>
    </li>
    @endif
    
    <li class="nav-item">
        <form action="{{ route('logout') }}" method="POST" class="d-inline w-100">
            @csrf
            <button type="submit" class="nav-link menu-item border-0 bg-transparent w-100 text-start">
                <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
            </button>
        </form>
    </li>
</ul>