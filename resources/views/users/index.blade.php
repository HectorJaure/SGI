@extends('layouts.app')

@section('title', 'Gestión de Usuarios - Sistema de Administración')

@section('header-title', 'Gestión de Usuarios')

@section('content')
<div class="container-fluid">
    <h1 class="page-title">Gestión de Usuarios del Sistema</h1>

    <!-- Mensajes -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Filtros -->
    <div class="filters">
        <form method="GET" action="{{ route('users.index') }}">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="filtro-departamento">Departamento</label>
                    <input list="departamentos-list" id="filtro-departamento" name="departamento" 
                        value="{{ request('departamento') }}" 
                        placeholder="Selecciona o escribe un departamento"
                        autocomplete="on" class="datalist-input">
                    <datalist id="departamentos-list">
                        <option value="">Todos los departamentos</option>
                        @foreach($departamentos as $departamento)
                            <option value="{{ $departamento }}">{{ $departamento }}</option>
                        @endforeach
                    </datalist>
                </div>
                <div class="filter-group">
                    <label for="filtro-rol">Rol</label>
                    <select id="filtro-rol" name="rol">
                        <option value="">Todos los roles</option>
                        <option value="Administrador" {{ request('rol') == 'Administrador' ? 'selected' : '' }}>Administrador</option>
                        <option value="Usuario" {{ request('rol') == 'Usuario' ? 'selected' : '' }}>Usuario</option>
                    </select>
                </div>
            </div>
            <div class="filter-row">
                <div class="filter-group">
                    <label for="filtro-busqueda">Buscar Usuario</label>
                    <input type="text" id="filtro-busqueda" name="search" placeholder="Buscar por nombre, email o username..." 
                        value="{{ request('search') }}">
                </div>
            </div>
            <div class="filter-actions d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1 d-flex justify-content-center align-items-center">
                    <i class="fas fa-filter"></i> Aplicar Filtros
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary flex-grow-1 d-flex justify-content-center align-items-center">
                    <i class="fas fa-redo"></i> Restablecer
                </a>
                <a href="{{ route('users.create') }}" class="btn btn-success flex-grow-1 d-flex justify-content-center align-items-center">
                    <i class="fas fa-plus"></i> Nuevo Usuario
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="content-card">
        <div class="card-header">
            <h3>Lista de Usuarios</h3>
            <div class="header-controls">
                <span style="font-size: 0.9rem; color: var(--gris-medio); margin-right: 15px;">
                    Total: {{ $users->total() }} usuarios
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

        <div class="table-container">
            <table id="users-table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Contacto</th>
                        <th>Departamento</th>
                        <th>Username</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>
                            <div class="user-info-cell">
                                <div class="user-avatar-cell">
                                    {{ strtoupper(substr($user->nombre, 0, 1)) }}
                                </div>
                                <div class="user-details">
                                    <h4>{{ $user->nombre }}</h4>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">{{ $user->email }}</div>
                            <div style="font-size: 12px; color: #7f8c8d;">{{ $user->telefono ?? 'N/A' }}</div>
                        </td>
                        <td>{{ $user->departamento ?? 'N/A' }}</td>
                        <td>{{ $user->username }}</td>
                        <td>
                            <span class="role-badge role-{{ strtolower($user->rol) }}">
                                {{ $user->rol }}
                            </span>
                        </td>
                        <td>
                            <div class="actions">
                                <button class="btn-icon btn-edit" title="Editar" onclick="editarUsuario({{ $user->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-icon btn-delete" title="Eliminar" 
                                    onclick="mostrarModalVerificarEliminar({{ $user->id }}, '{{ $user->nombre }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <div class="pagination">
            {{ $users->links('pagination::bootstrap-4') }}
        </div>
        <div class="pagination-info">
            Mostrando {{ $users->count() }} de {{ $users->total() }} usuarios
        </div>
    </div>
</div>
        
<!-- Modal para Verificar Contraseña - Eliminar -->
<div id="modalVerificarEliminar" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-shield-alt me-2"></i>Verificar Identidad</h3>
            <span class="close" onclick="cerrarModalVerificarEliminar()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Acción irreversible</strong>
            </div>
            <p id="textoVerificarEliminar" class="mb-3">Para eliminar usuarios, debe verificar su identidad ingresando su contraseña actual.</p>
            <div class="form-group">
                <label class="form-label">Contraseña Actual <span class="required">*</span></label>
                <div class="password-container">
                    <input type="password" id="password-eliminar" class="form-control"
                           placeholder="Ingrese su contraseña actual"
                           onkeypress="if(event.key === 'Enter') verificarPasswordParaEliminar()">
                    <button type="button" class="toggle-password" onclick="togglePasswordModal('password-eliminar', this)">
                        👁️
                    </button>
                </div>
                <div id="error-password-eliminar" class="form-help error" style="display: none;"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="cerrarModalVerificarEliminar()">
                <i class="fas fa-times me-2"></i>Cancelar
            </button>
            <button type="button" class="btn btn-danger" id="btnVerificarEliminar">
                <i class="fas fa-trash me-2"></i>Eliminar Usuario
            </button>
        </div>
    </div>
</div>

<!-- Modal para Autoeliminación -->
<div id="modalAutoEliminacion" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle me-2"></i>Acción No Permitida</h3>
            <span class="close" onclick="cerrarModalAutoEliminacion()">&times;</span>
        </div>
        <div class="modal-body">
            <div style="text-align: center; padding: 20px 0;">
                <div style="font-size: 48px; color: #e74c3c; margin-bottom: 15px;">⚠️</div>
                <h4 style="color: #e74c3c; margin-bottom: 10px;">No puede eliminar su propio usuario</h4>
                <p>Por seguridad, no está permitido eliminar su propia cuenta de usuario.</p>
                <p style="font-size: 14px; color: #7f8c8d; margin-top: 10px;">
                    Si necesita eliminar esta cuenta, contacte a otro administrador.
                </p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="cerrarModalAutoEliminacion()">
                <i class="fas fa-times me-2"></i>Entendido
            </button>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .content {
        padding: 30px;
        flex: 1;
    }
    
    .page-title {
        margin-bottom: 20px;
        color: var(--azul-marino);
        font-size: 1.8rem;
        font-weight: 600;
    }
    
    .content-card {
        background-color: var(--blanco);
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: var(--sombra);
        overflow-x: auto;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--gris-claro);
        background-color: white;
    }

    .section-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--azul-marino);
        margin: 0;
    }

    .table-controls {
        display: flex;
        gap: 15px;
        align-items: center;
    }

    .search-box {
        position: relative;
    }

    .search-box input {
        padding: 8px 15px 8px 35px;
        border: 1px solid #ddd;
        border-radius: 6px;
        width: 250px;
    }

    .search-box i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #7f8c8d;
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
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    
    .btn:hover {
        background-color: var(--azul-medio);
        transform: translateY(-2px);
    }
    
    .btn-success {
        background-color: var(--verde);
    }
    
    .btn-success:hover {
        background-color: #2f855a;
    }
    
    .btn-warning {
        background-color: var(--amarillo);
    }
    
    .btn-warning:hover {
        background-color: #b7791f;
    }
    
    .btn-danger {
        background-color: var(--rojo);
    }
    
    .btn-danger:hover {
        background-color: #c53030;
    }
    
    .btn-secondary {
        background-color: var(--gris-medio);
    }
    
    .btn-secondary:hover {
        background-color: var(--gris-oscuro);
    }

    /* Table */
    .table-container {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: #f8f9fa;
    }

    th {
        padding: 15px 20px;
        font-weight: 600;
        color: #2c3e50;
        border-bottom: 2px solid #ecf0f1;
        background-color: var(--gris-claro);
        text-align: center;
    }

    td {
        padding: 15px 20px;
        border-bottom: 1px solid #ecf0f1;
    }

    tbody tr:hover {
        background: #f8f9fa;
    }

    .user-info-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar-cell {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #3498db;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 14px;
    }

    .user-details h4 {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 2px;
    }

    .user-details p {
        font-size: 12px;
        color: #7f8c8d;
    }

    .role-badge {
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
    }

    .role-admin {
        background: #e3f2fd;
        color: #1976d2;
    }

    .role-user {
        background: #e8f5e8;
        color: #388e3c;
    }

    .actions {
        display: flex;
        gap: 8px;
    }

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

    /* Footer */
    .card-footer {
        padding: 15px 25px;
        border-top: 1px solid #ecf0f1;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8f9fa;
    }

    /* Filtros estilo requisitos legales */
    .filters {
        background-color: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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

    .filters label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.9rem;
    }

    .filters input, .filters select {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        background-color: white;
    }

    .filters input:focus, .filters select:focus {
        outline: none;
        border-color: var(--azul-claro);
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
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

    .alert-info {
        background-color: #e3f2fd;
        color: #1976d2;
        border-color: #bbdefb;
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

    .alert-warning {
        background-color: #fff3cd;
        color: #856404;
        border-color: #ffeaa7;
    }

    /* Estilos para el contenedor de contraseña */
    .password-container {
        position: relative;
        display: flex;
        align-items: center;
    }

    .password-container input {
        width: 100%;
        padding-right: 45px;
    }

    .toggle-password {
        position: absolute;
        right: 10px;
        background: none;
        border: none;
        cursor: pointer;
        font-size: 16px;
        padding: 5px;
    }

    .datalist-input {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }

    .datalist-input::-webkit-calendar-picker-indicator {
        display: none !important;
    }

    .datalist-input::-webkit-list-button {
        display: none !important;
    }

    .datalist-input::-webkit-clear-button {
        display: none !important;
    }

    /* Para Firefox */
    .datalist-input {
        background-image: none !important;
    }

    

    /* Responsive */
    @media (max-width: 768px) {
        .table-controls {
            flex-direction: column;
            align-items: stretch;
        }
        
        .search-box input {
            width: 100%;
        }

        .modal-content {
            width: 90%;
            margin: 20% auto;
        }

        .modal-footer {
            flex-direction: column;
        }

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
    }
</style>
@endsection

@section('scripts')
<script>
    let usuarioIdEliminar = null;
    let usuarioActualId = {{ session('user_id') }};

    document.addEventListener('DOMContentLoaded', function() {
        // Búsqueda en tiempo real
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const rows = document.querySelectorAll('#users-table tbody tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }

        // Filtro por departamento
        const departmentFilter = document.getElementById('department-filter');
        if (departmentFilter) {
            departmentFilter.addEventListener('change', function(e) {
                const selectedDept = e.target.value;
                const rows = document.querySelectorAll('#users-table tbody tr');
                
                rows.forEach(row => {
                    const deptCell = row.cells[2].textContent;
                    if (!selectedDept || deptCell === selectedDept) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

        // Configurar botón de verificación para eliminar
        const btnVerificarEliminar = document.getElementById('btnVerificarEliminar');
        if (btnVerificarEliminar) {
            btnVerificarEliminar.addEventListener('click', verificarPasswordParaEliminar);
        }

        // Cerrar modales al hacer clic fuera
        window.addEventListener('click', function(event) {
            const modalEliminar = document.getElementById('modalVerificarEliminar');
            const modalAutoEliminar = document.getElementById('modalAutoEliminacion');
            
            if (event.target === modalEliminar) {
                cerrarModalVerificarEliminar();
            }
            if (event.target === modalAutoEliminar) {
                cerrarModalAutoEliminacion();
            }
        });
    });

    function editarUsuario(id) {
        window.location.href = `/usuarios/${id}/editar`;
    }

    function mostrarModalVerificarEliminar(id, nombre) {
        // Verificar si es el usuario actual
        if (id === usuarioActualId) {
            mostrarModalAutoEliminacion();
        } else {
            usuarioIdEliminar = id;
            document.getElementById('textoVerificarEliminar').textContent = 
                `Para eliminar al usuario "${nombre}", debe verificar su identidad ingresando su contraseña actual.`;
            document.getElementById('modalVerificarEliminar').style.display = 'block';
            document.getElementById('password-eliminar').value = '';
            document.getElementById('error-password-eliminar').style.display = 'none';
            
            setTimeout(() => {
                document.getElementById('password-eliminar').focus();
            }, 100);
        }
    }

    function mostrarModalAutoEliminacion() {
        document.getElementById('modalAutoEliminacion').style.display = 'block';
    }

    function cerrarModalAutoEliminacion() {
        document.getElementById('modalAutoEliminacion').style.display = 'none';
    }

    function cerrarModalVerificarEliminar() {
        document.getElementById('modalVerificarEliminar').style.display = 'none';
        usuarioIdEliminar = null;
    }

    function togglePasswordModal(inputId, button) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
            button.textContent = '🔒';
        } else {
            input.type = 'password';
            button.textContent = '👁️';
        }
    }

    function verificarPasswordParaEliminar() {
        if (!usuarioIdEliminar) {
            mostrarError('No se ha seleccionado ningún usuario para eliminar');
            return;
        }

        const password = document.getElementById('password-eliminar').value;
        const errorDiv = document.getElementById('error-password-eliminar');

        if (!password) {
            mostrarError('La contraseña es requerida');
            return;
        }

        const btnVerificar = document.getElementById('btnVerificarEliminar');
        const originalText = btnVerificar.innerHTML;
        btnVerificar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Verificando...';
        btnVerificar.disabled = true;

        fetch('/verify-password', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ password: password })
        })
        .then(response => {
            if (response.status === 401) {
                return response.json().then(data => {
                    throw new Error(data.message || 'Contraseña incorrecta');
                });
            }
            
            if (!response.ok) {
                throw new Error(`Error del servidor`);
            }
            
            return response.json();
        })
        .then(data => {
            if (data.success) {
                document.getElementById('modalVerificarEliminar').style.display = 'none';
                eliminarUsuarioDirecto(usuarioIdEliminar, password);
            } else {
                mostrarError(data.message || 'Error desconocido');
            }
        })
        .catch(error => {
            if (error.message.includes('Contraseña incorrecta')) {
                mostrarError('Contraseña incorrecta. Por favor, intente nuevamente.');
            } else if (error.message.includes('Error del servidor')) {
                mostrarError('Error del servidor. Por favor, intente más tarde.');
            } else {
                mostrarError('Error: ' + error.message);
            }
        })
        .finally(() => {
            btnVerificar.innerHTML = originalText;
            btnVerificar.disabled = false;
        });
    }

    function eliminarUsuarioDirecto(id, password) {
        if (!id) return;

        const btnVerificar = document.getElementById('btnVerificarEliminar');
        const originalText = btnVerificar.innerHTML;
        btnVerificar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Eliminando...';
        btnVerificar.disabled = true;

        fetch(`/usuarios/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ current_password: password })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || 'Error en la respuesta del servidor');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                setTimeout(() => {
                    location.reload();
                }, 500);
            } else {
                throw new Error(data.message || 'Error al eliminar el usuario');
            }
        })
        .catch(error => {
            btnVerificar.innerHTML = originalText;
            btnVerificar.disabled = false;
            mostrarError('Error al eliminar el usuario: ' + error.message);
        });
    }

    function mostrarError(mensaje) {
        const errorDiv = document.getElementById('error-password-eliminar');
        if (errorDiv) {
            errorDiv.textContent = mensaje;
            errorDiv.style.display = 'block';
            document.getElementById('password-eliminar').classList.add('error');
            
            setTimeout(() => {
                document.getElementById('password-eliminar').focus();
                document.getElementById('password-eliminar').select();
            }, 100);
        }
    }

    // Hacer funciones globales
    window.editarUsuario = editarUsuario;
    window.mostrarModalVerificarEliminar = mostrarModalVerificarEliminar;
    window.verificarPasswordParaEliminar = verificarPasswordParaEliminar;
    window.cerrarModalVerificarEliminar = cerrarModalVerificarEliminar;
    window.togglePasswordModal = togglePasswordModal;
</script>
@endsection