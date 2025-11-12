@extends('layouts.app')

@section('title', 'Notificaciones - Sistema SGSST')

@section('header-title', 'Gestión de Notificaciones del Sistema')

@section('content')
<div class="container-fluid">
    <h1 class="page-title">Sistema de Gestión de Notificaciones</h1>
    
    <!-- Mensajes -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Indicadores de notificaciones -->
    <div class="risk-indicators">
        <div class="risk-indicator risk-medio">
            <div class="risk-value">{{ $notifications->total() }}</div>
            <div class="risk-label">Total Notificaciones</div>
        </div>
        <div class="risk-indicator risk-bajo">
            <div class="risk-value">{{ $unreadCount }}</div>
            <div class="risk-label">No Leídas</div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters">
        <form method="GET" action="{{ route('notifications.index') }}" id="filter-form">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="filter-type">Tipo de Notificación</label>
                    <select id="filter-type" name="tipo" class="form-select">
                        <option value="">Todos los tipos</option>
                        <option value="info" {{ request('tipo') == 'info' ? 'selected' : '' }}>Actualizado</option>
                        <option value="warning" {{ request('tipo') == 'warning' ? 'selected' : '' }}>Eliminado</option>
                        <option value="urgent" {{ request('tipo') == 'urgent' ? 'selected' : '' }}>Urgente</option>
                        <option value="success" {{ request('tipo') == 'success' ? 'selected' : '' }}>Creado</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filter-status">Estado</label>
                    <select id="filter-status" name="estado" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="no_leida" {{ request('estado') == 'no_leida' ? 'selected' : '' }}>No leídas</option>
                        <option value="leida" {{ request('estado') == 'leida' ? 'selected' : '' }}>Leídas</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filter-usuario">Realizado por</label>
                    <select id="filter-usuario" name="usuario" class="form-select">
                        <option value="">Todos los usuarios</option>
                        @foreach($usuariosAccion as $usuario)
                            <option value="{{ $usuario }}" {{ request('usuario') == $usuario ? 'selected' : '' }}>{{ $usuario }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="filter-actions d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1 d-flex justify-content-center align-items-center">
                    <i class="fas fa-filter me-2"></i> Aplicar Filtros
                </button>
                <a href="{{ route('notifications.index') }}" class="btn btn-secondary flex-grow-1 d-flex justify-content-center align-items-center">
                    <i class="fas fa-redo me-2"></i> Restablecer
                </a>
                <button type="button" class="btn btn-warning flex-grow-1 d-flex justify-content-center align-items-center" onclick="showMarkAllAsReadModal()">
                    <i class="fas fa-check-double me-2"></i> Marcar Todas Leídas
                </button>
                <button type="button" class="btn btn-danger flex-grow-1 d-flex justify-content-center align-items-center" onclick="showClearAllModal()">
                    <i class="fas fa-trash me-2"></i> Limpiar Todas
                </button>
            </div>
        </form>
    </div>

    <!-- Contenedor Principal de Notificaciones -->
    <div class="matrix-container">
        <div class="matrix-header">
            <h3 class="section-title">Lista de Notificaciones del Sistema</h3>
            <div class="header-controls">
                <span style="font-size: 0.9rem; color: var(--gris-medio); margin-right: 15px;">
                    Total: {{ $notifications->total() }} registros
                </span>
                <div class="pagination-selector">
                    <select id="perPage" name="per_page" onchange="changePerPage(this.value)" 
                            style="padding: 5px 10px; border: 1px solid var(--gris-claro); border-radius: 4px; font-size: 0.9rem;">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="notifications-list">
            @forelse($notifications as $notification)
            <div class="notification-item {{ $notification->estado == 'no_leida' ? 'unread' : '' }}"
                 data-type="{{ $notification->tipo }}" 
                 data-status="{{ $notification->estado }}"
                 data-usuario="{{ $notification->usuario_accion ?? '' }}">
                <div class="notification-icon">
                    @if($notification->tipo == 'urgent')
                        <i class="fas fa-exclamation-triangle"></i>
                    @elseif($notification->tipo == 'warning')
                        <i class="fas fa-exclamation-circle"></i>
                    @elseif($notification->tipo == 'success')
                        <i class="fas fa-check-circle"></i>
                    @else
                        <i class="fas fa-info-circle"></i>
                    @endif
                </div>
                <div class="notification-content">
                    <div class="notification-header">
                        <h4>{{ $notification->titulo }}</h4>
                        <div class="notification-badges">
                            @if($notification->estado == 'no_leida')
                                <span class="badge bg-primary">NUEVA</span>
                            @endif
                            <span class="badge badge-{{ $notification->tipo }}">
                                {{ ucfirst($notification->tipo) }}
                            </span>
                        </div>
                    </div>
                    <p class="notification-desc">{{ $notification->descripcion }}</p>
                    <div class="notification-footer">
                        <span class="notification-time">
                            <i class="fas fa-clock me-1"></i> 
                            {{ $notification->created_at->format('d/m/Y H:i') }}
                            ({{ $notification->created_at->diffForHumans() }})
                        </span>
                        <!-- Mostrar quién realizó la acción -->
                        @if($notification->usuario_accion && $notification->usuario_accion != 'Sistema')
                        <span class="notification-user">
                            <i class="fas fa-user me-1"></i>
                            Acción realizada por: {{ $notification->usuario_accion }}
                        </span>
                        @endif
                    </div>
                </div>
                <div class="notification-actions">
                    @if($notification->estado == 'no_leida')
                    <button class="btn-icon btn-edit" 
                            onclick="markAsRead({{ $notification->id }})"
                            title="Marcar como leída">
                        <i class="fas fa-check"></i>
                    </button>
                    @else
                    <button class="btn-icon btn-secondary" 
                            onclick="markAsUnread({{ $notification->id }})"
                            title="Marcar como no leída">
                        <i class="fas fa-undo"></i>
                    </button>
                    @endif
                    <button class="btn-icon btn-delete" 
                            onclick="showDeleteModal({{ $notification->id }}, '{{ addslashes($notification->titulo) }}')"
                            title="Eliminar notificación">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                <h4>No hay notificaciones</h4>
                <p class="text-muted">
                    @if(request()->hasAny(['tipo', 'estado', 'usuario']))
                        No se encontraron notificaciones con los filtros aplicados.
                    @else
                        No hay notificaciones en el sistema.
                    @endif
                </p>
                @if(request()->hasAny(['tipo', 'estado', 'usuario']))
                <a href="{{ route('notifications.index') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-redo me-2"></i> Mostrar Todas las Notificaciones
                </a>
                @endif
            </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
        <div class="card-footer">
            <div class="pagination-info">
                Mostrando {{ $notifications->firstItem() }} - {{ $notifications->lastItem() }} de {{ $notifications->total() }} notificaciones
            </div>
            <div class="pagination">
                {{ $notifications->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal para Eliminar Notificación Individual -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-trash me-2"></i>Eliminar Notificación</h3>
            <span class="close" onclick="closeDeleteModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>¿Está seguro de que desea eliminar esta notificación?</strong>
            </div>
            <p id="notificationToDeleteText" class="mb-3"></p>
            <p class="text-muted small">Esta acción no se puede deshacer.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">
                <i class="fas fa-times me-2"></i>Cancelar
            </button>
            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                <i class="fas fa-trash me-2"></i>Eliminar Notificación
            </button>
        </div>
    </div>
</div>

<!-- Modal para Marcar Todas como Leídas -->
<div id="markAllAsReadModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-check-double me-2"></i>Marcar Todas como Leídas</h3>
            <span class="close" onclick="closeMarkAllAsReadModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>¿Marcar todas las notificaciones como leídas?</strong>
            </div>
            <p class="mb-3">Se marcarán <strong>{{ $unreadCount }}</strong> notificaciones no leídas como leídas.</p>
            <p class="text-muted small">Esta acción actualizará el estado de todas las notificaciones no leídas.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeMarkAllAsReadModal()">
                <i class="fas fa-times me-2"></i>Cancelar
            </button>
            <button type="button" class="btn btn-warning" id="confirmMarkAllAsReadBtn">
                <i class="fas fa-check-double me-2"></i>Marcar Todas como Leídas
            </button>
        </div>
    </div>
</div>

<!-- Modal para Limpiar Todas las Notificaciones -->
<div id="clearAllModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-trash-alt me-2"></i>Limpiar Todas las Notificaciones</h3>
            <span class="close" onclick="closeClearAllModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>¡ADVERTENCIA! Esta acción es irreversible</strong>
            </div>
            <p class="mb-3">Se eliminarán <strong>{{ $notifications->total() }}</strong> notificaciones del sistema.</p>
            <ul class="text-muted small">
                <li>Se perderán todas las notificaciones existentes</li>
                <li>Esta acción no se puede deshacer</li>
                <li>Las notificaciones eliminadas no se pueden recuperar</li>
            </ul>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeClearAllModal()">
                <i class="fas fa-times me-2"></i>Cancelar
            </button>
            <button type="button" class="btn btn-danger" id="confirmClearAllBtn">
                <i class="fas fa-trash-alt me-2"></i>Eliminar Todas las Notificaciones
            </button>
        </div>
    </div>
</div>

<style>
/* Variables CSS */
:root {
    --azul-marino: #1e3a5f;
    --azul-medio: #2c5282;
    --azul-claro: #4299e1;
    --gris-oscuro: #4a5568;
    --gris-medio: #718096;
    --gris-claro: #e2e8f0;
    --blanco: #ffffff;
    --verde: #38a169;
    --amarillo: #d69e2e;
    --naranja: #dd6b20;
    --rojo: #e53e3e;
    --sombra: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --sombra-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Estilos generales */
.page-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--azul-marino);
    margin-bottom: 1.5rem;
    text-align: center;
}

/* Botones */
.btn {
    background-color: var(--azul-claro);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    font-size: 0.9rem;
}

.btn:hover {
    background-color: var(--azul-medio);
    transform: translateY(-2px);
}

.btn-primary { background-color: var(--azul-claro); }
.btn-primary:hover { background-color: var(--azul-medio); }
.btn-warning { background-color: var(--amarillo); }
.btn-warning:hover { background-color: #b7791f; }
.btn-danger { background-color: var(--rojo); }
.btn-danger:hover { background-color: #c53030; }
.btn-secondary { background-color: var(--gris-medio); }
.btn-secondary:hover { background-color: var(--gris-oscuro); }

/* Botones de acción (íconos) */
.btn-icon {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.btn-edit {
    background: #e3f2fd;
    color: #1976d2;
}

.btn-edit:hover {
    background: #1976d2;
    color: white;
}

.btn-delete {
    background: #fdeaea;
    color: #e74c3c;
}

.btn-delete:hover {
    background: #e74c3c;
    color: white;
}

.btn-secondary {
    background: #f8f9fa;
    color: #6c757d;
    border: 1px solid #dee2e6;
}

.btn-secondary:hover {
    background: #6c757d;
    color: white;
}

/* Indicadores de riesgo */
.risk-indicators {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.risk-indicator {
    background-color: var(--blanco);
    border-radius: 12px;
    padding: 20px;
    box-shadow: var(--sombra);
    text-align: center;
    transition: all 0.3s ease;
    border-left: 5px solid transparent;
}

.risk-indicator:hover {
    transform: translateY(-2px);
    box-shadow: var(--sombra-hover);
}

.risk-bajo { border-left-color: var(--verde); }
.risk-medio { border-left-color: var(--amarillo); }

.risk-value {
    font-size: 2.2rem;
    font-weight: 800;
    margin-bottom: 8px;
    color: var(--azul-marino);
}

.risk-label {
    font-size: 0.95rem;
    color: var(--gris-medio);
    font-weight: 500;
}

/* Filtros */
.filters {
    background-color: var(--blanco);
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: var(--sombra);
}

.filter-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.filter-group {
    margin-bottom: 15px;
}

.filter-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--gris-oscuro);
    font-size: 0.9rem;
}

input, select {
    width: 100%;
    padding: 10px 14px;
    border: 2px solid var(--gris-claro);
    border-radius: 8px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    background-color: var(--blanco);
}

input:focus, select:focus {
    outline: none;
    border-color: var(--azul-claro);
    box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
}

/* Contenedor de matriz */
.matrix-container {
    background-color: var(--blanco);
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: var(--sombra);
    overflow-x: auto;
}

.matrix-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid var(--gris-claro);
}

.section-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--azul-marino);
    margin: 0;
}

/* Lista de Notificaciones */
.notifications-list {
    min-height: 400px;
}

.notification-item {
    display: flex;
    align-items: flex-start;
    padding: 20px;
    border-bottom: 1px solid var(--gris-claro);
    transition: all 0.3s ease;
    gap: 15px;
    background: var(--blanco);
}

.notification-item.unread {
    background: linear-gradient(90deg, #f0f9ff 0%, #ffffff 100%);
    border-left: 4px solid var(--azul-claro);
}

.notification-item:hover {
    background-color: #f8fafc;
    transform: translateX(5px);
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-icon {
    font-size: 24px;
    margin-top: 5px;
    flex-shrink: 0;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    background: #f8f9fa;
}

.notification-item.unread .notification-icon {
    background: #e3f2fd;
}

.notification-icon .fa-exclamation-triangle { color: var(--rojo); }
.notification-icon .fa-exclamation-circle { color: var(--naranja); }
.notification-icon .fa-check-circle { color: var(--verde); }
.notification-icon .fa-info-circle { color: var(--azul-claro); }

.notification-content {
    flex: 1;
}

.notification-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 8px;
    gap: 10px;
}

.notification-header h4 {
    color: var(--azul-marino);
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0;
    flex: 1;
}

.notification-badges {
    display: flex;
    gap: 5px;
    flex-shrink: 0;
    flex-wrap: wrap;
}

.badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-urgent {
    background-color: var(--rojo);
    color: white;
}

.badge-warning {
    background-color: var(--naranja);
    color: white;
}

.badge-success {
    background-color: var(--verde);
    color: white;
}

.badge-info {
    background-color: var(--azul-claro);
    color: white;
}

.notification-desc {
    color: var(--gris-oscuro);
    margin-bottom: 10px;
    line-height: 1.5;
    font-size: 0.9rem;
}

.notification-footer {
    display: flex;
    gap: 20px;
    font-size: 0.8rem;
    color: var(--gris-medio);
    flex-wrap: wrap;
    align-items: center;
}

.notification-actions {
    display: flex;
    gap: 8px;
    flex-shrink: 0;
    align-items: center;
}

/* Estados vacíos */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--gris-medio);
}

.empty-state h4 {
    color: var(--gris-medio);
    margin-bottom: 10px;
    font-weight: 600;
}

/* Nuevos estilos para el usuario que realizó la acción */
.notification-user {
    font-size: 0.75rem;
    color: var(--gris-medio);
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Alertas */
.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 0.9rem;
    font-weight: 500;
    border: 1px solid transparent;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-success {
    background-color: #c6f6d5;
    color: #22543d;
    border-color: #9ae6b4;
}

.alert-danger {
    background-color: #fed7d7;
    color: #742a2a;
    border-color: #feb2b2;
}

.alert-warning {
    background-color: #fef5c4;
    color: #744210;
    border-color: #faf089;
}

.alert-info {
    background-color: #c6f6f5;
    color: #234e52;
    border-color: #81e6d9;
}

/* Footer de la tarjeta */
.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 25px;
    border-top: 2px solid var(--gris-claro);
    background: #f8f9fa;
}

/* Estilos para modales */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    animation: fadeIn 0.3s ease;
}

.modal-content {
    background-color: white;
    margin: 10% auto;
    padding: 0;
    border-radius: 10px;
    width: 500px;
    max-width: 90%;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    animation: modalSlideIn 0.3s ease;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.modal-header {
    padding: 20px 25px;
    border-bottom: 1px solid #ecf0f1;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    color: #2c3e50;
    font-size: 18px;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
}

.close {
    color: #7f8c8d;
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
    transition: color 0.3s ease;
}

.close:hover {
    color: #e74c3c;
}

.modal-body {
    padding: 25px;
    color: #2c3e50;
    font-size: 16px;
    line-height: 1.5;
}

.modal-footer {
    padding: 20px 25px;
    border-top: 1px solid #ecf0f1;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

/* Responsive */
@media (max-width: 768px) {
    .filter-row {
        grid-template-columns: 1fr;
    }

    .filter-actions {
        flex-direction: column;
    }

    .filter-actions .btn {
        width: 100%;
        justify-content: center;
    }

    .matrix-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }

    .notification-item {
        flex-direction: column;
        gap: 10px;
    }

    .notification-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .notification-actions {
        align-self: flex-end;
    }

    .card-footer {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }

    .modal-content {
        width: 95%;
        margin: 5% auto;
    }

    .modal-footer {
        flex-direction: column;
    }

    .modal-footer .btn {
        width: 100%;
        justify-content: center;
    }

    .notification-footer {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .notification-user {
        font-size: 0.7rem;
    }
}
</style>

<script>
// Variables globales
let currentNotificationId = null;

// Funciones para mostrar/ocultar modales
function showDeleteModal(id, titulo) {
    currentNotificationId = id;
    document.getElementById('notificationToDeleteText').textContent = `"${titulo}"`;
    document.getElementById('deleteModal').style.display = 'block';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    currentNotificationId = null;
}

function showMarkAllAsReadModal() {
    document.getElementById('markAllAsReadModal').style.display = 'block';
}

function closeMarkAllAsReadModal() {
    document.getElementById('markAllAsReadModal').style.display = 'none';
}

function showClearAllModal() {
    document.getElementById('clearAllModal').style.display = 'block';
}

function closeClearAllModal() {
    document.getElementById('clearAllModal').style.display = 'none';
}

// Cerrar modales al hacer clic fuera
window.addEventListener('click', function(event) {
    const modals = ['deleteModal', 'markAllAsReadModal', 'clearAllModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (event.target === modal) {
            modal.style.display = 'none';
            if (modalId === 'deleteModal') {
                currentNotificationId = null;
            }
        }
    });
});

// Funciones JavaScript para acciones
function markAsRead(notificationId) {
    fetch(`/notificaciones/${notificationId}/marcar-leida`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            setTimeout(() => location.reload(), 1000);
        } 
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function markAsUnread(notificationId) {
    fetch(`/notificaciones/${notificationId}/marcar-no-leida`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            setTimeout(() => location.reload(), 1000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Configurar event listeners para los botones de confirmación
document.addEventListener('DOMContentLoaded', function() {
    // Eliminar notificación individual
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (currentNotificationId) {
            deleteNotification(currentNotificationId);
        }
    });

    // Marcar todas como leídas
    document.getElementById('confirmMarkAllAsReadBtn').addEventListener('click', function() {
        markAllAsRead();
    });

    // Limpiar todas las notificaciones
    document.getElementById('confirmClearAllBtn').addEventListener('click', function() {
        clearAllNotifications();
    });
});

function deleteNotification(notificationId) {
    fetch(`/notificaciones/${notificationId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeDeleteModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            closeDeleteModal();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        closeDeleteModal();
    });
}

function markAllAsRead() {
    fetch('/notificaciones/marcar-todas-leidas', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeMarkAllAsReadModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            closeMarkAllAsReadModal();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        closeMarkAllAsReadModal();
    });
}

function clearAllNotifications() {
    fetch('/notificaciones', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeClearAllModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            closeClearAllModal();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        closeClearAllModal();
    });
}

// Función de filtros
function applyFilters() {
    const typeFilter = document.getElementById('filter-type').value;
    const statusFilter = document.getElementById('filter-status').value;
    const usuarioFilter = document.getElementById('filter-usuario').value;
    
    const notifications = document.querySelectorAll('.notification-item');
    let visibleCount = 0;
    
    notifications.forEach(notification => {
        const type = notification.getAttribute('data-type');
        const status = notification.getAttribute('data-status');
        const usuario = notification.getAttribute('data-usuario');
        
        const typeMatch = !typeFilter || type === typeFilter;
        const statusMatch = !statusFilter || status === statusFilter;
        const usuarioMatch = !usuarioFilter || usuario === usuarioFilter;
        
        const shouldShow = typeMatch && statusMatch && usuarioMatch;
        notification.style.display = shouldShow ? 'flex' : 'none';
        
        if (shouldShow) visibleCount++;
    });

    // Mostrar mensaje si no hay resultados
    const emptyState = document.querySelector('.empty-state');
    if (emptyState) {
        if (visibleCount === 0 && document.querySelectorAll('.notification-item').length > 0) {
            if (!document.querySelector('.no-results-message')) {
                const noResults = document.createElement('div');
                noResults.className = 'empty-state no-results-message';
                noResults.innerHTML = `
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4>No se encontraron notificaciones</h4>
                    <p class="text-muted">No hay notificaciones que coincidan con los filtros aplicados.</p>
                    <button class="btn btn-primary mt-3" onclick="resetFilters()">
                        <i class="fas fa-redo me-2"></i> Mostrar Todas las Notificaciones
                    </button>
                `;
                document.querySelector('.notifications-list').appendChild(noResults);
            }
        } else {
            const noResultsMsg = document.querySelector('.no-results-message');
            if (noResultsMsg) {
                noResultsMsg.remove();
            }
        }
    }
}

function resetFilters() {
    document.getElementById('filter-type').value = '';
    document.getElementById('filter-status').value = '';
    document.getElementById('filter-usuario').value = '';
    applyFilters();
}

</script>
@endsection