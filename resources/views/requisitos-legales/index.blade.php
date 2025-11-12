@extends('layouts.app')

@section('title', 'Matriz de Requisitos Legales - Sistema SGSST')

@section('header-title', 'Matriz de Requisitos Legales - ISO 45001:2018')

@section('content')
<div class="container-fluid">
    <h1 class="page-title">Matriz de Requisitos Legales</h1>
    
    <!-- Mensajes -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Indicadores de cumplimiento -->
    <div class="risk-indicators">
        @php
            $total = $requisitos->count();
            $cumplidos = $requisitos->where('cumplimiento', 'si')->count();
            $noCumplidos = $requisitos->where('cumplimiento', 'no')->count();
            $porcentajeCumplido = $total > 0 ? round(($cumplidos / $total) * 100) : 0;
        @endphp
        <div class="risk-indicator risk-bajo">
            <div class="risk-value">{{ $total }}</div>
            <div class="risk-label">Total Requisitos</div>
        </div>
        <div class="risk-indicator risk-medio">
            <div class="risk-value">{{ $cumplidos }}</div>
            <div class="risk-label">Requisitos Cumplidos</div>
        </div>
        <div class="risk-indicator risk-alto">
            <div class="risk-value">{{ $noCumplidos }}</div>
            <div class="risk-label">Requisitos Pendientes</div>
        </div>
        <div class="risk-indicator risk-muy-alto">
            <div class="risk-value">{{ $porcentajeCumplido }}%</div>
            <div class="risk-label">Porcentaje Cumplimiento</div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters">
        <form method="GET" action="{{ route('requisitos-legales.index') }}">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="filtro-norma">Norma</label>
                    <select id="filtro-norma" name="norma">
                        <option value="">Todas las normas</option>
                        @foreach($normas as $norma)
                            <option value="{{ $norma }}" {{ request('norma') == $norma ? 'selected' : '' }}>{{ $norma }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filtro-cumplimiento">Cumplimiento</label>
                    <select id="filtro-cumplimiento" name="cumplimiento">
                        <option value="">Todos los estados</option>
                        <option value="si" {{ request('cumplimiento') == 'si' ? 'selected' : '' }}>Cumplido</option>
                        <option value="no" {{ request('cumplimiento') == 'no' ? 'selected' : '' }}>No Cumplido</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filtro-tipo">Tipo de Requisito</label>
                    <select id="filtro-tipo" name="tipo_requisito">
                        <option value="">Todos los tipos</option>
                        @foreach($tiposRequisito as $tipo)
                            <option value="{{ $tipo }}" {{ request('tipo_requisito') == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filtro-fecha">Fecha de Cumplimiento</label>
                    <input type="date" id="filtro-fecha" name="fecha_cumplimiento" 
                        value="{{ request('fecha_cumplimiento') }}">
                </div>
            </div>
            <div class="filter-row">
                <div class="filter-group">
                    <label for="filtro-responsable">Responsable</label>
                    <input type="text" id="filtro-responsable" name="responsable" placeholder="Buscar por responsable..." 
                        value="{{ request('responsable') }}">
                </div>
                <div class="filter-group">
                    <label for="filtro-peligro">Peligro Asociado</label>
                    <input type="text" id="filtro-peligro" name="peligro_asociado" placeholder="Buscar por peligro..." 
                        value="{{ request('peligro_asociado') }}">
                </div>
            </div>
            <div class="filter-actions d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1 d-flex justify-content-center align-items-center">
                    <i class="fas fa-filter"></i> Aplicar Filtros
                </button>
                <a href="{{ route('requisitos-legales.index') }}" class="btn btn-secondary flex-grow-1 d-flex justify-content-center align-items-center">
                    <i class="fas fa-redo"></i> Restablecer
                </a>
                <a href="{{ route('requisitos-legales.create') }}" class="btn btn-success flex-grow-1 d-flex justify-content-center align-items-center">
                    <i class="fas fa-plus"></i> Nuevo Requisito
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla de Requisitos -->
    <div class="matrix-container">
        <div class="matrix-header">
            <h3 class="section-title">Lista de Requisitos Legales</h3>
            <div class="header-controls">
                <span style="font-size: 0.9rem; color: var(--gris-medio); margin-right: 15px;">
                    Total: {{ $requisitos->total() }} registros
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
        
        <table>
            <thead>
                <tr>
                    <th rowspan="2">Norma</th>
                    <th rowspan="2">Título</th>
                    <th rowspan="2">Tipo</th>
                    <th rowspan="2">No. Requisito</th>
                    <th rowspan="2">Descripción</th>
                    <th colspan="3" class="subheader">CUMPLIMIENTO</th>
                    <th rowspan="2">Peligro Asociado</th>
                    <th rowspan="2">Fecha Cumplimiento</th>
                    <th rowspan="2">Responsables</th>
                    <th rowspan="2">Frecuencia Control</th>
                    <th rowspan="2">Responsable Control</th>
                    <th rowspan="2">Acciones</th>
                </tr>
                <tr>
                    <th class="category-header">Estado</th>
                    <th class="category-header">Evidencia</th>
                    <th class="category-header">Acciones No</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requisitos as $requisito)
                <tr>
                    <td><strong>{{ $requisito->norma }}</strong></td>
                    <td>{{ $requisito->titulo }}</td>
                    <td>{{ $requisito->tipo_requisito }}</td>
                    <td>{{ $requisito->numero_requisito }}</td>
                    <td class="peligro-cell">{{ Str::limit($requisito->descripcion, 100) }}</td>
                    
                    @if($requisito->cumplimiento == 'si')
                        <td class="evaluation-cell">
                            <span class="significancia baja">CUMPLIDO</span>
                        </td>
                        <td class="evaluation-cell">{{ $requisito->evidencia ?: '-' }}</td>
                        <td class="evaluation-cell">-</td>
                    @else
                        <td class="evaluation-cell">
                            <span class="significancia alta">PENDIENTE</span>
                        </td>
                        <td class="evaluation-cell">-</td>
                        <td class="evaluation-cell">{{ $requisito->acciones_no ?: '-' }}</td>
                    @endif
                    
                    <td>{{ $requisito->peligro_asociado }}</td>
                    <td class="evaluation-cell">{{ $requisito->fecha_cumplimiento->format('d/m/Y') }}</td>
                    <td>{{ $requisito->responsables }}</td>
                    <td class="evaluation-cell">{{ $requisito->frecuencia_control }}</td>
                    <td>{{ $requisito->responsable_control }}</td>
                    <td class="evaluation-cell">
                        <div class="actions">
                            <a href="{{ route('requisitos-legales.edit', $requisito->id) }}" 
                               class="btn-icon btn-edit" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('requisitos-legales.destroy', $requisito->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-icon btn-delete" 
                                        onclick="mostrarModalEliminarRequisito({{ $requisito->id }}, '{{ addslashes($requisito->titulo) }} ({{ addslashes($requisito->norma) }})')"
                                        title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="14" class="text-center" style="padding: 20px;">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No se encontraron requisitos legales
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">
        {{ $requisitos->links('pagination::bootstrap-4') }}
    </div>
    <div class="pagination-info">
        Mostrando {{ $requisitos->count() }} de {{ $requisitos->total() }} requisitos legales
    </div>
</div>

<!-- Modal para Eliminar Requisito Legal -->
<div id="modalEliminar" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación</h3>
            <span class="close" onclick="cerrarModalEliminar()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>¿Está seguro de que desea eliminar este requisito legal?</strong>
            </div>
            <p id="textoRequisitoEliminar" class="mb-3"></p>
            <p class="text-muted small">Esta acción no se puede deshacer.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="cerrarModalEliminar()">
                <i class="fas fa-times me-2"></i>Cancelar
            </button>
            <form id="formEliminarRequisito" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash me-2"></i>Eliminar
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Variables CSS (las mismas que en matrix.blade.php) */
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

    /* Botones (mismos estilos que matrix) */
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
    .btn-success { background-color: var(--verde); }
    .btn-success:hover { background-color: #2f855a; }
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
        text-decoration: none;
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

    /* Indicadores de riesgo (mismo estilo que matrix) */
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
    .risk-alto { border-left-color: var(--naranja); }
    .risk-muy-alto { border-left-color: var(--rojo); }

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

    /* Filtros (mismo estilo que matrix) */
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

    /* Contenedor de matriz (mismo estilo que matrix) */
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

    /* Tabla (mismo estilo que matrix) */
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 1600px;
        border-radius: 8px;
        overflow: hidden;
    }

    th {
        text-align: center;
        padding: 14px 10px;
        background-color: var(--gris-claro);
        color: var(--gris-oscuro);
        font-weight: 700;
        font-size: 0.85rem;
        border: 1px solid var(--gris-medio);
    }

    td {
        padding: 12px 10px;
        border-bottom: 1px solid var(--gris-claro);
        font-size: 0.85rem;
        border: 1px solid var(--gris-claro);
        vertical-align: middle;
        text-align: center;
    }

    tr:hover {
        background-color: rgba(66, 153, 225, 0.03);
    }

    .subheader {
        background-color: var(--azul-marino) !important;
        color: white !important;
        text-align: center;
        font-weight: 700;
    }

    .category-header {
        background-color: var(--azul-medio) !important;
        color: white !important;
        text-align: center;
        font-weight: 600;
    }

    .peligro-cell {
        max-width: 300px;
        min-width: 280px;
        text-align: left !important;
    }

    .evaluation-cell {
        text-align: center;
        font-weight: 600;
    }

    /* Significancia (para estados de cumplimiento) */
    .significancia {
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 700;
        text-align: center;
        display: inline-block;
        min-width: 100px;
        border: 2px solid transparent;
    }

    .significancia.baja {
        background-color: #c6f6d5;
        color: #22543d;
        border-color: #9ae6b4;
    }

    .significancia.alta {
        background-color: #fed7d7;
        color: #742a2a;
        border-color: #feb2b2;
    }

    /* Acciones */
    .actions {
        display: flex;
        gap: 6px;
        justify-content: center;
        align-items: center;
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

        .actions {
            flex-direction: column;
            gap: 4px;
        }

        .btn-icon {
            width: 28px;
            height: 28px;
        }
    }

    /* Centrar todo el contenido de la tabla */
    th, td {
        text-align: center !important;
        vertical-align: middle !important;
    }

    .peligro-cell {
        text-align: left !important;
    }

    th[rowspan] {
        vertical-align: middle !important;
    }
</style>
@endsection

@section('scripts')
<script>
    // Funciones para eliminar requisito legal
    function mostrarModalEliminarRequisito(id, textoRequisito) {
        document.getElementById('textoRequisitoEliminar').textContent = textoRequisito;
        document.getElementById('formEliminarRequisito').action = `/requisitos-legales/${id}`;
        document.getElementById('modalEliminar').style.display = 'block';
    }

    function cerrarModalEliminar() {
        document.getElementById('modalEliminar').style.display = 'none';
        document.getElementById('formEliminarRequisito').action = '';
    }

    // Cerrar modal al hacer clic fuera
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('modalEliminar');
        if (event.target === modal) {
            cerrarModalEliminar();
        }
    });
</script>
@endsection