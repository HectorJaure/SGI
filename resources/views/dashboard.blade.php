@extends('layouts.app')

@section('title', 'Dashboard - Sistema SGSST')
@section('header-title', 'Inicio SGSST')

@section('content')
<div class="container-fluid">
    <h1 class="page-title">Dashboard del Sistema de Gestión</h1>
    
    <!-- Status Banner -->
    <div class="status-banner content-card mb-4">
        <div class="card-body">
            <div class="status-content">
                <div class="status-info">
                    <h3 class="mb-1">Estado del SGSST: {{ $estado_sgsst['general'] }}</h3>
                    <p class="text-muted mb-0">Última actualización: {{ date('d/m/Y') }}</p>
                </div>
                <div class="status-indicator text-end">
                    <div class="status-level" style="color: {{ $estado_sgsst['color_estado'] }};">
                        {{ $estado_sgsst['nivel_cumplimiento'] }}%
                    </div>
                    <div class="status-dates text-muted small">
                        Próxima auditoría: {{ date('d/m/Y', strtotime($estado_sgsst['proxima_auditoria'])) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas Rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card users">
                <h3>Total Riesgos</h3>
                <div class="number">{{ $metricas['total_riesgos'] }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card active">
                <h3>Riesgos Altos</h3>
                <div class="number">{{ $metricas['riesgos_alto_impacto'] + $metricas['riesgos_muy_alto'] }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card roles">
                <h3>Cumplimiento</h3>
                <div class="number">{{ $estado_sgsst['nivel_cumplimiento'] }}%</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card inactive">
                <h3>Alertas Activas</h3>
                <div class="number">{{ count($alertas_urgentes) }}</div>
            </div>
        </div>
    </div>

    <!-- Combined Metrics and Alerts Section -->
    <div class="row">
        <div class="col-lg-6">
            <div class="content-card h-100">
                <div class="card-header">
                    <h3 class="card-title mb-0">Distribución de Riesgos</h3>
                </div>
                <div class="card-body">
                    <div class="risk-chart-container">
                        <!-- Gráfica circular -->
                        <div class="chart-wrapper">
                            <svg class="chart-svg" viewBox="0 0 42 42">
                                <circle class="chart-background" cx="21" cy="21" r="15.9155" />
                                
                                @php
                                    $total = $metricas['total_riesgos'];
                                    if($total > 0) {
                                        $porcentaje_alto = (($metricas['riesgos_alto_impacto'] + $metricas['riesgos_muy_alto']) / $total) * 100;
                                        $porcentaje_medio = ($metricas['riesgos_mediano_impacto'] / $total) * 100;
                                        $porcentaje_bajo = ($metricas['riesgos_bajo_impacto'] / $total) * 100;
                                        
                                        // CÁLCULOS CORREGIDOS - Usando la circunferencia completa (100)
                                        $circumference = 100;
                                        
                                        // El primer segmento empieza en 25 (parte superior)
                                        $offset_alto = 25;
                                        // El segundo segmento empieza donde termina el primero
                                        $offset_medio = $offset_alto - $porcentaje_alto;
                                        // El tercer segmento empieza donde termina el segundo
                                        $offset_bajo = $offset_medio - $porcentaje_medio;
                                    } else {
                                        $porcentaje_alto = $porcentaje_medio = $porcentaje_bajo = 0;
                                        $circumference = 100;
                                        $offset_alto = $offset_medio = $offset_bajo = 25;
                                    }
                                @endphp
                                
                                @if($total > 0)
                                    @if($porcentaje_alto > 0)
                                    <circle class="chart-segment segment-alto"
                                        cx="21" cy="21" r="15.9155"
                                        stroke-dasharray="{{ $porcentaje_alto }} {{ $circumference - $porcentaje_alto }}"
                                        stroke-dashoffset="{{ $offset_alto }}" />
                                    @endif
                                    
                                    @if($porcentaje_medio > 0)
                                    <circle class="chart-segment segment-medio"
                                        cx="21" cy="21" r="15.9155"
                                        stroke-dasharray="{{ $porcentaje_medio }} {{ $circumference - $porcentaje_medio }}"
                                        stroke-dashoffset="{{ $offset_medio }}" />
                                    @endif
                                    
                                    @if($porcentaje_bajo > 0)
                                    <circle class="chart-segment segment-bajo"
                                        cx="21" cy="21" r="15.9155"
                                        stroke-dasharray="{{ $porcentaje_bajo }} {{ $circumference - $porcentaje_bajo }}"
                                        stroke-dashoffset="{{ $offset_bajo }}" />
                                    @endif
                                @else
                                <circle class="chart-segment segment-bajo"
                                    cx="21" cy="21" r="15.9155"
                                    stroke-dasharray="100 0"
                                    stroke-dashoffset="25" />
                                @endif
                            </svg>
                            
                            <div class="chart-center">
                                <div class="chart-total">{{ $metricas['total_riesgos'] }}</div>
                                <div class="chart-label">Total Riesgos</div>
                            </div>
                        </div>
                        
                        <!-- Leyenda -->
                        <div class="chart-legend">
                            <div class="legend-item">
                                <div class="legend-color alto"></div>
                                <div class="legend-info">
                                    <div class="legend-value">{{ $metricas['riesgos_alto_impacto'] + $metricas['riesgos_muy_alto'] }}</div>
                                    <div class="legend-label">Alto Impacto</div>
                                </div>
                                <div class="legend-percentage">{{ $total > 0 ? round($porcentaje_alto) : 0 }}%</div>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color medio"></div>
                                <div class="legend-info">
                                    <div class="legend-value">{{ $metricas['riesgos_mediano_impacto'] }}</div>
                                    <div class="legend-label">Medio Impacto</div>
                                </div>
                                <div class="legend-percentage">{{ $total > 0 ? round($porcentaje_medio) : 0 }}%</div>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color bajo"></div>
                                <div class="legend-info">
                                    <div class="legend-value">{{ $metricas['riesgos_bajo_impacto'] }}</div>
                                    <div class="legend-label">Bajo Impacto</div>
                                </div>
                                <div class="legend-percentage">{{ $total > 0 ? round($porcentaje_bajo) : 0 }}%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="content-card h-100">
                <div class="card-header">
                    <h3 class="card-title mb-0">Alertas Urgentes</h3>
                </div>
                <div class="card-body p-0">
                    @if(count($alertas_urgentes) > 0)
                        @foreach($alertas_urgentes as $alerta)
                        <div class="alert-item">
                            <div class="alert-icon {{ strtolower($alerta['tipo']) }}">
                                @switch($alerta['tipo'])
                                    @case('Riesgo') ⚠️ @break
                                    @case('Requisito') 📋 @break
                                    @default ℹ️ @break
                                @endswitch
                            </div>
                            <div class="alert-content">
                                <div class="alert-title">{{ $alerta['titulo'] }}</div>
                                <div class="alert-desc">{{ $alerta['descripcion'] }}</div>
                                <div class="alert-meta">
                                    <div class="alert-date">{{ date('d/m/Y', strtotime($alerta['fecha'])) }}</div>
                                    <span class="priority-badge priority-{{ strtolower($alerta['prioridad']) }}">
                                        {{ $alerta['prioridad'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-check-circle fa-2x mb-3"></i>
                                <p>No hay alertas urgentes en este momento</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="content-card mt-4">
        <div class="card-header">
            <h3 class="card-title mb-0">Acciones Rápidas</h3>
        </div>
        <div class="card-body">
            <div class="quick-actions">
                <a href="{{ route('risks.matrix') }}" class="action-btn">
                    <div class="action-icon">📊</div>
                    <div class="action-text">Gestionar Matriz de Riesgos</div>
                </a>
                <a href="{{ route('requisitos-legales.index') }}" class="action-btn">
                    <div class="action-icon">📋</div>
                    <div class="action-text">Revisar Requisitos Legales</div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Stats Cards Styles */
    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-left: 4px solid #3498db;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .stat-card.users {
        border-left-color: #3498db;
    }

    .stat-card.active {
        border-left-color: #27ae60;
    }

    .stat-card.inactive {
        border-left-color: #e74c3c;
    }

    .stat-card.roles {
        border-left-color: #f39c12;
    }

    .stat-card h3 {
        font-size: 14px;
        color: #7f8c8d;
        margin-bottom: 10px;
        text-transform: uppercase;
        font-weight: 500;
    }

    .stat-card .number {
        font-size: 32px;
        font-weight: 700;
        color: #2c3e50;
    }

    /* Status Banner */
    .status-banner {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 30px;
        border-left: 6px solid #27ae60;
    }

    .status-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .status-info h3 {
        color: #2c3e50;
        font-size: 20px;
        margin-bottom: 5px;
    }

    .status-info p {
        color: #7f8c8d;
    }

    .status-indicator {
        text-align: right;
    }

    .status-level {
        font-size: 32px;
        font-weight: 700;
        color: #27ae60;
    }

    .status-dates {
        font-size: 14px;
        color: #7f8c8d;
        margin-top: 5px;
    }

    /* Risk Chart */
    .risk-chart-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        align-items: center;
    }

    .chart-wrapper {
        position: relative;
        height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .chart-svg {
        width: 200px;
        height: 200px;
        transform: rotate(-90deg);
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));
    }

    .chart-background {
        fill: none;
        stroke: #f8f9fa;
        stroke-width: 10;
    }

    .chart-segment {
        fill: none;
        stroke-width: 10;
        stroke-linecap: round;
        transition: all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
        animation: chartAnimation 1.5s ease-out forwards;
    }

    @keyframes chartAnimation {
        from {
            stroke-dasharray: 0 100;
        }
    }

    .segment-alto {
        stroke: #e74c3c;
    }

    .segment-medio {
        stroke: #f39c12;
    }

    .segment-bajo {
        stroke: #27ae60;
    }

    .chart-center {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        background: white;
        border-radius: 50%;
        width: 80px;
        height: 80px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .chart-total {
        font-size: 24px;
        font-weight: 800;
        color: #2c3e50;
        line-height: 1;
        margin-bottom: 2px;
    }

    .chart-label {
        font-size: 10px;
        color: #7f8c8d;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .chart-legend {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 8px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .legend-item:hover {
        transform: translateX(5px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .legend-color {
        width: 20px;
        height: 20px;
        border-radius: 6px;
        flex-shrink: 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .legend-color.alto {
        background: #e74c3c;
    }

    .legend-color.medio {
        background: #f39c12;
    }

    .legend-color.bajo {
        background: #27ae60;
    }

    .legend-info {
        flex: 1;
    }

    .legend-value {
        font-weight: 700;
        color: #2c3e50;
        font-size: 18px;
        line-height: 1;
        margin-bottom: 2px;
    }

    .legend-label {
        font-size: 11px;
        color: #7f8c8d;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .legend-percentage {
        font-weight: 800;
        color: #2c3e50;
        font-size: 16px;
        background: white;
        padding: 4px 8px;
        border-radius: 6px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Alerts List */
    .alert-item {
        display: flex;
        align-items: flex-start;
        padding: 15px 20px;
        border-bottom: 1px solid #ecf0f1;
        transition: background-color 0.3s ease;
    }

    .alert-item:hover {
        background: #f8f9fa;
    }

    .alert-item:last-child {
        border-bottom: none;
    }

    .alert-icon {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        flex-shrink: 0;
        font-size: 16px;
    }

    .alert-icon.riesgo {
        background: #fdeaea;
        color: #e74c3c;
    }

    .alert-icon.requisito {
        background: #e3f2fd;
        color: #3498db;
    }

    .alert-icon.sistema {
        background: #f0f8f0;
        color: #27ae60;
    }

    .alert-content {
        flex: 1;
    }

    .alert-title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 4px;
    }

    .alert-desc {
        font-size: 13px;
        color: #7f8c8d;
        margin-bottom: 4px;
    }

    .alert-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .alert-date {
        font-size: 12px;
        color: #7f8c8d;
    }

    .priority-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
    }

    .priority-alta {
        background: #fdeaea;
        color: #e74c3c;
    }

    .priority-media {
        background: #fff3e0;
        color: #f39c12;
    }

    .priority-baja {
        background: #f0f8f0;
        color: #27ae60;
    }

    /* Quick Actions */
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }

    .action-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 25px;
        background: #f8f9fa;
        border: 2px dashed #ddd;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        color: #333;
    }

    .action-btn:hover {
        border-color: #3498db;
        background: #e3f2fd;
        transform: translateY(-2px);
        text-decoration: none;
        color: #333;
    }

    .action-icon {
        font-size: 28px;
        margin-bottom: 10px;
    }

    .action-text {
        font-size: 14px;
        font-weight: 500;
        text-align: center;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .risk-chart-container {
            grid-template-columns: 1fr;
        }
        
        .quick-actions {
            grid-template-columns: 1fr;
        }
        
        .status-content {
            flex-direction: column;
            text-align: center;
            gap: 15px;
        }
        
        .status-indicator {
            text-align: center;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animación de la gráfica
        const chartSegments = document.querySelectorAll('.chart-segment');
        
        chartSegments.forEach((segment, index) => {
            segment.style.animationDelay = `${index * 0.3}s`;
        });

        // Efectos hover para las tarjetas de métricas
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });
</script>
@endsection