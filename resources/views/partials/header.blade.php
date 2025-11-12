<header class="bg-white shadow-sm p-3">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="h4 mb-0" style="@media (max-width: 768px) {.h4{font-size: calc(10rem + .3vw);}}">@yield('header-title', 'Sistema SGSST')</h2>
        <div class="d-flex align-items-center gap-3">
            <!-- Notifications Dropdown - Solo para administradores -->
            @if(session('user_rol') === 'Administrador')
            <div class="dropdown">
                <button class="btn btn-light position-relative" type="button" 
                        data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell"></i>
                    @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ $unreadNotificationsCount }}
                    </span>
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="width: 350px;">
                    <li><h6 class="dropdown-header">Notificaciones</h6></li>
                    @if(isset($unreadNotifications) && $unreadNotifications->count() > 0)
                        @foreach($unreadNotifications as $notification)
                        <li>
                            <a class="dropdown-item" href="{{ route('notifications.index') }}">
                                <div class="d-flex w-100 justify-content-between">
                                    <small class="text-{{ $notification->tipo == 'urgent' ? 'danger' : ($notification->tipo == 'warning' ? 'warning' : 'info') }}">
                                        <i class="fas fa-{{ $notification->tipo == 'urgent' ? 'exclamation-triangle' : ($notification->tipo == 'warning' ? 'exclamation-circle' : 'info-circle') }} me-1"></i>
                                        {{ $notification->titulo }}
                                    </small>
                                    <small>{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <small class="text-muted">{{ Str::limit($notification->descripcion, 50) }}</small>
                            </a>
                        </li>
                        @endforeach
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-center" href="{{ route('notifications.index') }}">
                                Ver todas las notificaciones
                            </a>
                        </li>
                    @else
                        <li><a class="dropdown-item text-center text-muted" href="#">No hay notificaciones nuevas</a></li>
                    @endif
                </ul>
            </div>
            @endif

            <!-- User Info -->
            <div class="user-info d-flex align-items-center">
                <div class="user-avatar bg-light rounded-circle d-flex align-items-center justify-content-center me-2" 
                     style="width: 40px; height: 40px;">
                    {{ strtoupper(substr(session('usuario', 'A'), 0, 1)) }}
                </div>
                <div>
                    <div class="fw-bold">{{ session('usuario', 'Administrador') }}</div>
                    <div class="small text-muted">{{ session('user_rol', 'Usuario') }}</div>
                </div>
            </div>
        </div>
    </div>
</header>
<style>
@media (max-width: 768px) {
    .h4 {
       font-size: calc(1rem + .3vw);
    }
}
</style>