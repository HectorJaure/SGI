@extends('layouts.app')

@section('title', 'Matriz de Riesgos - Sistema SGSST')

@section('header-title', 'Matriz de Identificación y Evaluación de Peligros')

@section('content')
<div class="container-fluid">
    <h1 class="page-title">Matriz de Riesgos de Seguridad y Salud Ocupacional</h1>
    
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
    
    <!-- Indicadores de riesgo -->
    <div class="risk-indicators">
        <div class="risk-indicator risk-bajo">
            <div class="risk-value">{{ $contadores['bajo'] }}</div>
            <div class="risk-label">Riesgos Bajos</div>
        </div>
        <div class="risk-indicator risk-medio">
            <div class="risk-value">{{ $contadores['medio'] }}</div>
            <div class="risk-label">Riesgos Medios</div>
        </div>
        <div class="risk-indicator risk-alto">
            <div class="risk-value">{{ $contadores['alto'] }}</div>
            <div class="risk-label">Riesgos Altos</div>
        </div>
        <div class="risk-indicator risk-muy-alto">
            <div class="risk-value">{{ $contadores['muy_alto'] }}</div>
            <div class="risk-label">Riesgos Muy Altos</div>
        </div>
    </div>
    
    <!-- Pestañas -->
    <div class="tabs">
        <div class="tab active" data-tab="matriz">Matriz de Peligros</div>
        <div class="tab" data-tab="probabilidad">Criterios de Probabilidad</div>
        <div class="tab" data-tab="consecuencia">Criterios de Consecuencia</div>
    </div>
    
    <!-- Pestaña 1: Matriz de Peligros -->
    <div class="tab-content active" id="matriz-tab">
        <!-- Filtros -->
        <div class="filters">
            <form method="GET" action="{{ route('risks.matrix') }}">
                <!-- Primera línea: Solo los dos datalist -->
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="filtro-lugar">Lugar</label>
                        <input list="lugar-list" id="filtro-lugar" name="lugar" 
                            value="{{ request('lugar') }}" 
                            placeholder="Seleccionalo o escribelo"
                            autocomplete="on" class="datalist-input">
                        <datalist id="lugar-list">
                            <option value="">Todos los lugares</option>
                            @foreach($lugares as $lugar)
                                <option value="{{ $lugar }}">{{ $lugar }}</option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="filter-group">
                        <label for="filtro-actividad">Actividad</label>
                        <input list="actividades-list" id="filtro-actividad" name="actividad" 
                            value="{{ request('actividad') }}" 
                            placeholder="Seleccionala o escribela"
                            autocomplete="on" class="datalist-input">
                        <datalist id="actividades-list">
                            <option value="">Todas las actividades</option>
                            @foreach($actividades as $actividad)
                                <option value="{{ $actividad }}">{{ $actividad }}</option>
                            @endforeach
                        </datalist>
                    </div>
                </div>

                <!-- Segunda línea: Los demás filtros -->
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="filtro-tipo-riesgo">Tipo de Riesgo</label>
                        <select id="filtro-tipo-riesgo" name="tipo_riesgo">
                            <option value="">Todos los tipos</option>
                            <option value="Interno" {{ request('tipo_riesgo') == 'Interno' ? 'selected' : '' }}>Interno</option>
                            <option value="Externo" {{ request('tipo_riesgo') == 'Externo' ? 'selected' : '' }}>Externo</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="filtro-clasificacion">Clasificación</label>
                        <select id="filtro-clasificacion" name="clasificacion">
                            <option value="">Todas las clasificaciones</option>
                            <option value="Seguridad" {{ request('clasificacion') == 'Seguridad' ? 'selected' : '' }}>Seguridad</option>
                            <option value="Salud" {{ request('clasificacion') == 'Salud' ? 'selected' : '' }}>Salud</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="filtro-nivel-riesgo">Nivel de Riesgo</label>
                        <select id="filtro-nivel-riesgo" name="nivel_riesgo">
                            <option value="">Todos los niveles</option>
                            <option value="baja" {{ request('nivel_riesgo') == 'baja' ? 'selected' : '' }}>Bajo</option>
                            <option value="media" {{ request('nivel_riesgo') == 'media' ? 'selected' : '' }}>Medio</option>
                            <option value="alta" {{ request('nivel_riesgo') == 'alta' ? 'selected' : '' }}>Alto</option>
                            <option value="muy-alta" {{ request('nivel_riesgo') == 'muy-alta' ? 'selected' : '' }}>Muy Alto</option>
                        </select>
                    </div>
                </div>

                <div class="filter-actions d-flex gap-2">
                    <!-- Los botones se mantienen igual -->
                    <button type="submit" class="btn btn-primary flex-grow-1 d-flex justify-content-center align-items-center">
                        <i class="fas fa-filter"></i> Aplicar Filtros
                    </button>
                    <a href="{{ route('risks.matrix') }}" class="btn btn-secondary flex-grow-1 d-flex justify-content-center align-items-center">
                        <i class="fas fa-redo"></i> Restablecer
                    </a>
                    <a href="{{ route('risks.create') }}" class="btn btn-success flex-grow-1 d-flex justify-content-center align-items-center">
                        <i class="fas fa-plus"></i> Nuevo Registro
                    </a>
                    <button type="button" class="btn btn-warning flex-grow-1 d-flex justify-content-center align-items-center" 
                            onclick="exportarActa()" id="btn-exportar-pdf">
                        <i class="fas fa-file-export"></i> Exportar acta de verificacion
                    </button>
                </div>
            </form>
        </div>
        
        <div class="matrix-container">
            <div class="matrix-header">
                <h3 class="section-title">Matriz de Identificación y Evaluación de Peligros</h3>
                    <div class="header-controls">
                        <span style="font-size: 0.9rem; color: var(--gris-medio); margin-right: 15px;">
                            Total: {{ $riesgos->total() }} riesgos
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
                        <th rowspan="3">Lugar</th>
                        <th rowspan="3">Actividad</th>
                        <th rowspan="3" class="peligro-cell">Peligro</th>
                        <th rowspan="3">Tipo de Riesgo</th>
                        <th rowspan="3">Otros Factores</th>
                        <th rowspan="3">Clasificación</th>
                        <th colspan="6" class="subheader">EVALUACIÓN DEL RIESGO</th>
                        <th rowspan="3">Significancia</th>
                        <th rowspan="3">Nivel de Riesgo</th>
                        <th rowspan="3">Acciones</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="category-header">PROBABILIDADES</th>
                        <th colspan="3" class="category-header">CONSECUENCIAS</th>
                    </tr>
                    <tr>
                        <!-- Probabilidades -->
                        <th class="category-header">Tiempo de Exposición</th>
                        <th class="category-header">No. de Personas Expuestas</th>
                        <th class="category-header">Probabilidad de Ocurrencia</th>
                        <!-- Consecuencias -->
                        <th class="category-header">Consecuencia a Infraestructura/Equipos</th>
                        <th class="category-header">Consecuencia a las Personas</th>
                        <th class="category-header">Total</th>
                    </tr>
                </thead>
                <tbody id="tabla-riesgos">
                    @forelse($riesgosAgrupados as $grupo)
                        @foreach($grupo['riesgos'] as $index => $riesgo)
                            @php
                                $probabilidadTotal = $riesgo->tiempo_exposicion + $riesgo->personas_expuestas + $riesgo->probabilidad_ocurrencia;
                                $consecuenciaTotal = $riesgo->consecuencia_infraestructura + $riesgo->consecuencia_personas;
                                $claseRiesgo = 'significancia ' . $riesgo->nivel_riesgo;
                                $rowspan = count($grupo['riesgos']) > 1 && $index === 0 ? 'rowspan="' . count($grupo['riesgos']) . '"' : '';
                            @endphp
                            <tr>
                                @if($index === 0)
                                    <td {!! $rowspan !!}>{{ $grupo['lugar'] }}</td>
                                    <td {!! $rowspan !!}>{{ $grupo['actividad'] }}</td>
                                @endif
                                <td class="peligro-cell">{{ $riesgo->peligro }}</td>
                                <td class="evaluation-cell">{{ $riesgo->tipo_riesgo }}</td>
                                <td class="evaluation-cell">{{ $riesgo->otros_factores }}</td>
                                <td class="evaluation-cell">{{ $riesgo->clasificacion }}</td>
                                <td class="evaluation-cell">{{ $riesgo->tiempo_exposicion }}</td>
                                <td class="evaluation-cell">{{ $riesgo->personas_expuestas }}</td>
                                <td class="evaluation-cell">{{ $riesgo->probabilidad_ocurrencia }}</td>
                                <td class="evaluation-cell">{{ $riesgo->consecuencia_infraestructura }}</td>
                                <td class="evaluation-cell">{{ $riesgo->consecuencia_personas }}</td>
                                <td class="evaluation-cell consequence-total">{{ number_format($consecuenciaTotal, 1) }}</td>
                                <td class="evaluation-cell">{{ number_format($riesgo->significancia, 1) }}</td>
                                <td class="evaluation-cell">
                                    <span class="{{ $claseRiesgo }}">
                                        @if($riesgo->nivel_riesgo == 'baja')
                                            BAJO
                                        @elseif($riesgo->nivel_riesgo == 'media')
                                            MEDIO
                                        @elseif($riesgo->nivel_riesgo == 'alta')
                                            ALTO
                                        @else
                                            MUY ALTO
                                        @endif
                                    </span>
                                </td>
                                <td class="evaluation-cell">
                                    <div class="actions">
                                        <a href="{{ route('risks.edit', $riesgo->id) }}" class="btn-icon btn-edit" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('risks.destroy', $riesgo->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn-icon btn-delete" 
                                                    onclick="mostrarModalEliminarRiesgo({{ $riesgo->id }}, '{{ $riesgo->lugar }} - {{ $riesgo->actividad }}')"
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="15" class="text-center" style="padding: 20px;">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No se encontraron riesgos
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">
            {{ $riesgos->links('pagination::bootstrap-4') }}
        </div>
        <div class="pagination-info">
            Mostrando {{ $riesgos->count() }} riesgos (agrupados en {{ count($riesgosAgrupados) }} grupos) de {{ $riesgos->total() }} total
        </div>
    </div>
    
    <!-- Pestaña 2: Criterios de Probabilidad -->
    <div class="tab-content" id="probabilidad-tab">
        <div class="matrix-container">
            <h3 class="section-title">Criterios de Evaluación - Probabilidad</h3>
            
            <div class="criteria-single-column">
                <div class="criteria-section">
                    <h4 class="criteria-title">Tiempo de Exposición</h4>
                    <div class="table-container">
                        <table class="criteria-table">
                            <thead>
                                <tr>
                                    <th>Tiempo</th>
                                    <th>Explicación</th>
                                    <th>Puntaje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Menor</td>
                                    <td>No rutinaria</td>
                                    <td>1.0</td>
                                </tr>
                                <tr>
                                    <td>Todo el tiempo</td>
                                    <td>Rutinario</td>
                                    <td>5.0</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="criteria-section">
                    <h4 class="criteria-title">Número de Personas Expuestas</h4>
                    <div class="table-container">
                        <table class="criteria-table">
                            <thead>
                                <tr>
                                    <th>No. de Personas</th>
                                    <th>Puntaje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1 a 5</td>
                                    <td>1.0</td>
                                </tr>
                                <tr>
                                    <td>6 - 10</td>
                                    <td>2.0</td>
                                </tr>
                                <tr>
                                    <td>11 - 50</td>
                                    <td>3.0</td>
                                </tr>
                                <tr>
                                    <td>51 a 500</td>
                                    <td>4.0</td>
                                </tr>
                                <tr>
                                    <td>Más de 500</td>
                                    <td>5.0</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="criteria-section">
                    <h4 class="criteria-title">Probabilidad de Ocurrencia</h4>
                    <div class="table-container">
                        <table class="criteria-table">
                            <thead>
                                <tr>
                                    <th>Probabilidad</th>
                                    <th>Explicación</th>
                                    <th>Puntaje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Baja</td>
                                    <td>Existen muchas condiciones inseguras pero no se han presentado incidentes</td>
                                    <td>1.0</td>
                                </tr>
                                <tr>
                                    <td>Mediana</td>
                                    <td>Por lo menos ha ocurrido 1 incidente</td>
                                    <td>3.0</td>
                                </tr>
                                <tr>
                                    <td>Alta</td>
                                    <td>Ha ocurrido más de 1 incidente</td>
                                    <td>5.0</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pestaña 3: Criterios de Consecuencia -->
    <div class="tab-content" id="consecuencia-tab">
        <div class="matrix-container">
            <h3 class="section-title">Criterios de Evaluación - Consecuencia</h3>
            
            <div class="criteria-single-column">
                <div class="criteria-section">
                    <h4 class="criteria-title">Consecuencia del Peligro a las Personas</h4>
                    <div class="table-container">
                        <table class="criteria-table">
                            <thead>
                                <tr>
                                    <th>Personas</th>
                                    <th>Tipo de Daño</th>
                                    <th>Puntaje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Baja</td>
                                    <td>Cortadura (Sin sangrado activo), Hematoma (Excepto en cabeza), Enfermedades Osteomusculares (Torceduras), Quemaduras de primer grado no mayor al 10% del cuerpo, Enfermedades infecciosas de bajo contagio (ejem. Otitis, Conjuntivitis, Gastroenteritis, etc.)</td>
                                    <td>1.0</td>
                                </tr>
                                <tr>
                                    <td>Mediana</td>
                                    <td>Quemaduras de 1er o 2do grado que no pasen del 50% del área corporal, Fractura, esguince, Enfermedades Infecciosas (ejem. Infecciones respiratorias agudas)</td>
                                    <td>3.0</td>
                                </tr>
                                <tr>
                                    <td>Alta</td>
                                    <td>Quemaduras de 1er, 2do o 3er grado y mayor al 50%, quemaduras por inhalación, Intoxicación, discapacidad permanente (Mutilación, Amputación o Anquilosis), daño permanente, enfermedades ocupacional (ejem. Audición, Visión, estrés, Articulaciones, etc.), muerte</td>
                                    <td>5.0</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="criteria-section">
                    <h4 class="criteria-title">Consecuencia del Peligro a Infraestructura/Equipos</h4>
                    <div class="table-container">
                        <table class="criteria-table">
                            <thead>
                                <tr>
                                    <th>Equipo o Infraestructura</th>
                                    <th>Tipo de Daño</th>
                                    <th>Puntaje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Extremadamente Baja</td>
                                    <td>Sin costo</td>
                                    <td>0.0</td>
                                </tr>
                                <tr>
                                    <td>Baja</td>
                                    <td>Reparación con un costo entre $1 a $5,000 pesos</td>
                                    <td>1.0</td>
                                </tr>
                                <tr>
                                    <td>Mediana</td>
                                    <td>Reparación con un costo entre $5,001 a $50,000 pesos</td>
                                    <td>2.0</td>
                                </tr>
                                <tr>
                                    <td>Alta</td>
                                    <td>Reparación con un costo Mayor a $50,000 pesos</td>
                                    <td>3.0</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="criteria-section">
                    <h4 class="criteria-title">Cálculo de Significancia</h4>
                    <div class="significancia-info">
                        <p><strong>Fórmula:</strong> Significancia = (Tiempo de Exposición + No. de Personas Expuestas + Probabilidad de Ocurrencia) × (Consecuencia a Infraestructura + Consecuencia a Personas)</p>
                        <p><strong>Límites:</strong></p>
                        <ul>
                            <li>Límite superior: (5+5+5) × (3+5) = 15 × 8 = 120</li>
                            <li>Límite inferior para riesgo alto: (5+5+5) × 5 = 15 × 5 = 75</li>
                            <li>Límite inferior para riesgo medio: (5+5+5) × 3 = 15 × 3 = 45</li>
                            <li>Límite inferior para riesgo bajo: < 45</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Eliminar Riesgo -->
<div id="modalEliminar" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación</h3>
            <span class="close" onclick="cerrarModalEliminar()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>¿Está seguro de que desea eliminar este riesgo?</strong>
            </div>
            <p id="textoRiesgoEliminar" class="mb-3"></p>
            <p class="text-muted small">Esta acción no se puede deshacer.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="cerrarModalEliminar()">
                <i class="fas fa-times me-2"></i>Cancelar
            </button>
            <form id="formEliminarRiesgo" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash me-2"></i>Eliminar Riesgo
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Variables CSS mejoradas */
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
    
    /* Botones al estilo de index.blade.php */
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
    
    .btn-primary {
        background-color: var(--azul-claro);
    }
    
    .btn-primary:hover {
        background-color: var(--azul-medio);
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
    
    /* Filtros mejorados */
    .filters {
        background-color: var(--blanco);
        border-radius: 0 0 12px 12px;
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
    
    /* Resto de estilos se mantienen igual */
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
    
    /* Pestañas mejoradas */
    .tabs {
        display: flex;
        background-color: var(--blanco);
        border-radius: 12px 12px 0 0;
        overflow: hidden;
        box-shadow: var(--sombra);
        margin-bottom: 0;
    }
    
    .tab {
        padding: 18px 30px;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
        font-weight: 600;
        color: var(--gris-oscuro);
        flex: 1;
        text-align: center;
        background: none;
        border: none;
        font-size: 0.95rem;
    }
    
    .tab.active {
        border-bottom: 3px solid var(--azul-claro);
        color: var(--azul-claro);
        background-color: rgba(66, 153, 225, 0.05);
    }
    
    .tab:hover:not(.active) {
        background-color: var(--gris-claro);
        color: var(--azul-medio);
    }
    
    /* Contenedores de pestañas */
    .tab-content {
        display: none;
        animation: fadeIn 0.3s ease-in;
    }
    
    .tab-content.active {
        display: block;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Matriz de riesgos mejorada */
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
    
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 1400px;
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
        vertical-align: top;
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
    }
    
    .evaluation-cell {
        text-align: center;
        font-weight: 600;
    }
    
    .probability-total, .consequence-total {
        background-color: var(--gris-claro);
        font-weight: 700;
        text-align: center;
    }
    
    /* Significancia mejorada */
    .significancia {
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 700;
        text-align: center;
        display: inline-block;
        min-width: 70px;
        border: 2px solid transparent;
    }
    
    .significancia.baja {
        background-color: #c6f6d5;
        color: #22543d;
        border-color: #9ae6b4;
    }
    
    .significancia.media {
        background-color: #fefcbf;
        color: #744210;
        border-color: #faf089;
    }
    
    .significancia.alta {
        background-color: #fed7d7;
        color: #742a2a;
        border-color: #feb2b2;
    }
    
    .significancia.muy-alta {
        background-color: #742a2a;
        color: white;
        border-color: #e53e3e;
    }
    
    /* Acciones mejoradas */
    .actions {
        display: flex;
        gap: 6px;
        justify-content: center;
    }
    
    /* Criterios mejorados */
    .criteria-section {
        margin-bottom: 30px;
    }
    
    .criteria-title {
        font-size: 1.2rem;
        color: var(--azul-marino);
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 2px solid var(--azul-claro);
        font-weight: 700;
    }
    
    .criteria-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: var(--sombra);
    }
    
    .criteria-table th {
        background-color: var(--azul-marino);
        color: white;
        text-align: left;
        padding: 14px;
        font-weight: 600;
    }
    
    .criteria-table td {
        padding: 12px 14px;
        border: 1px solid var(--gris-claro);
    }
    
    .criteria-table tr:nth-child(even) {
        background-color: rgba(226, 232, 240, 0.3);
    }
    
    /* Alertas mejoradas */
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

    /* Responsive mejorado */
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
        
        .tabs {
            flex-wrap: wrap;
        }
        
        .tab {
            flex: 1;
            min-width: 120px;
            padding: 15px 10px;
            font-size: 0.85rem;
        }
        
        .matrix-header {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }
        
        .btn {
            width: 100%;
            justify-content: center;
        }
    }

    /* Centrar todo el contenido de la tabla */
    th, td {
        text-align: center !important;
        vertical-align: middle !important;
    }

    .peligro-cell {
        max-width: 300px;
        min-width: 280px;
        text-align: center !important;
    }

    .evaluation-cell {
        text-align: center !important;
        font-weight: 600;
    }

    .consequence-total {
        text-align: center !important;
    }

    .actions {
        display: flex;
        gap: 6px;
        justify-content: center;
        align-items: center;
    }

    .subheader, .category-header {
        text-align: center !important;
    }

    th[rowspan] {
        vertical-align: middle !important;
    }

    /* Centrar específicamente todas las celdas de datos */
    #tabla-riesgos td {
        text-align: center !important;
        vertical-align: middle !important;
    }

    /* Centrar las celdas de cabecera */
    #tabla-riesgos th {
        text-align: center !important;
        vertical-align: middle !important;
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

    .datalist-input::-webkit-inner-spin-button,
    .datalist-input::-webkit-outer-spin-button {
        display: none !important;
    }

    /* Para Firefox */
    .datalist-input {
        background-image: none !important;
    }

    /* Estilos para disposición de una sola columna */
.criteria-single-column {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.criteria-section {
    background-color: var(--blanco);
    border-radius: 8px;
    padding: 25px;
    box-shadow: var(--sombra);
    border-left: 4px solid var(--azul-claro);
    width: 100%;
}

/* Contenedor para tablas responsivas */
.table-container {
    width: 100%;
    overflow-x: auto;
    border-radius: 6px;
    border: 1px solid var(--gris-claro);
    margin-top: 15px;
}

/* Estilos para tablas de criterios */
.criteria-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 100%;
}

.criteria-table th {
    background-color: var(--azul-marino);
    color: white;
    padding: 14px 16px;
    text-align: left;
    font-weight: 600;
    font-size: 0.9rem;
    white-space: nowrap;
}

.criteria-table td {
    padding: 12px 16px;
    border: 1px solid var(--gris-claro);
    vertical-align: top;
    font-size: 0.9rem;
    line-height: 1.5;
    word-wrap: break-word;
}

.criteria-table tr:nth-child(even) {
    background-color: rgba(226, 232, 240, 0.3);
}

/* Ajustes para columnas de texto largo */
.criteria-table td:nth-child(2) { /* Segunda columna (Explicación/Tipo de Daño) */
    min-width: 300px;
}

.criteria-table td:nth-child(1),
.criteria-table td:nth-child(3) { /* Primera y tercera columna */
    white-space: nowrap;
    min-width: 120px;
    width: 15%;
}

.criteria-table td:nth-child(2) { /* Segunda columna */
    width: 70%;
}

/* Responsive */
@media (max-width: 768px) {
    .criteria-section {
        padding: 20px;
    }
    
    .criteria-table th,
    .criteria-table td {
        padding: 10px 12px;
        font-size: 0.85rem;
    }
    
    .table-container {
        margin: 0 -5px;
        border: none;
    }
    
    .criteria-table td:nth-child(2) {
        min-width: 200px;
    }
}

@media (max-width: 480px) {
    .criteria-section {
        padding: 15px;
    }
    
    .criteria-table {
        font-size: 0.8rem;
    }
    
    .criteria-table th,
    .criteria-table td {
        padding: 8px 10px;
    }
    
    .criteria-table td:nth-child(2) {
        min-width: 150px;
    }
}

</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Manejo de pestañas
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');
                mostrarTab(tabName);
            });
        });
    });

    function mostrarTab(tabName) {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
        document.getElementById(`${tabName}-tab`).classList.add('active');
    }

    // Funciones para eliminar riesgo
    function mostrarModalEliminarRiesgo(id, textoRiesgo) {
        document.getElementById('textoRiesgoEliminar').textContent = textoRiesgo;
        document.getElementById('formEliminarRiesgo').action = `/risks/${id}`;
        document.getElementById('modalEliminar').style.display = 'block';
    }

    function cerrarModalEliminar() {
        document.getElementById('modalEliminar').style.display = 'none';
        document.getElementById('formEliminarRiesgo').action = '';
    }

    // Cerrar modal al hacer clic fuera
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('modalEliminar');
        if (event.target === modal) {
            cerrarModalEliminar();
        }
    });

    function exportarActa() {
        const btnExportar = document.getElementById('btn-exportar-pdf');
        const originalText = btnExportar.innerHTML;
        btnExportar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando PDF...';
        btnExportar.disabled = true;
        
        try {
            const url = "{{ route('risks.export-pdf') }}";
            window.location.href = url;
            
        } catch (error) {
            console.error('Error al exportar PDF:', error);
            alert('Error al generar el PDF. Por favor, intente nuevamente.');
        } finally {
            setTimeout(() => {
                btnExportar.innerHTML = originalText;
                btnExportar.disabled = false;
            }, 3000);
        }
    }
</script>
@endsection