@extends('layouts.app')

@section('title', 'Dashboard - Sistema SGSST')
@section('header-title', 'Inicio SGSST')

@section('content')
<div class="container-fluid">
    <h1 class="page-title">Dashboard del Sistema de Gestión</h1>
    
    <div class="status-banner content-card mb-4">
        <div class="card-body">
            <div class="status-content">
                <div class="status-info">
                    <h3 class="mb-1">Estado del SGSST:</h3>
                </div>
                <div class="status-indicator text-end">
                    <div class="status-level" style="color: {{ $estado_sgsst['color_estado'] }};">
                        {{ $estado_sgsst['nivel_cumplimiento'] }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card users">
                <h3>Total Riesgos</h3>
                <div class="number">{{ $metricas['total_riesgos'] }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card active">
                <h3>Riesgos Altos</h3>
                <div class="number">{{ $metricas['riesgos_alto_impacto'] }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card inactive">
                <h3>Alertas Activas</h3>
                <div class="number">{{ count($alertas_urgentes) }}</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="content-card h-100">
                <div class="card-header">
                    <h3 class="card-title mb-0">Distribución de Riesgos</h3>
                </div>
                <div class="card-body">
                    <div class="risk-chart-container">
                        <div class="chart-wrapper">
                            <svg class="chart-svg" viewBox="0 0 42 42">
                                <circle class="chart-background" cx="21" cy="21" r="15.9155" />
                                
                                @php
                                    $total = $metricas['total_riesgos'];
                                    if($total > 0) {
                                        $alto_count = $metricas['riesgos_alto_impacto'];
                                        $medio_count = $metricas['riesgos_mediano_impacto'];
                                        $bajo_count = $metricas['riesgos_bajo_impacto'];
                                        
                                        $porcentaje_alto = ($alto_count / $total) * 100;
                                        $porcentaje_medio = ($medio_count / $total) * 100;
                                        $porcentaje_bajo = ($bajo_count / $total) * 100;
                                        
                                        $circumference = 100;
                                        $offset_base = 25;
                                        
                                    } else {
                                        $alto_count = $medio_count = $bajo_count = 0;
                                        $porcentaje_alto = $porcentaje_medio = $porcentaje_bajo = 0;
                                        $circumference = 100;
                                        $offset_base = 25;
                                    }
                                @endphp
                                
                                @if($total > 0)
                                    @if($porcentaje_alto > 0)
                                    <circle class="chart-segment segment-alto"
                                        cx="21" cy="21" r="15.9155"
                                        stroke-dasharray="{{ $porcentaje_alto }} {{ $circumference - $porcentaje_alto }}"
                                        stroke-dashoffset="{{ $offset_base }}" />
                                    @endif
                                    
                                    @if($porcentaje_medio > 0)
                                    <circle class="chart-segment segment-medio"
                                        cx="21" cy="21" r="15.9155"
                                        stroke-dasharray="{{ $porcentaje_medio }} {{ $circumference - $porcentaje_medio }}"
                                        stroke-dashoffset="{{ $offset_base - $porcentaje_alto }}" />
                                    @endif
                                    
                                    @if($porcentaje_bajo > 0)
                                    <circle class="chart-segment segment-bajo"
                                        cx="21" cy="21" r="15.9155"
                                        stroke-dasharray="{{ $porcentaje_bajo }} {{ $circumference - $porcentaje_bajo }}"
                                        stroke-dashoffset="{{ $offset_base - $porcentaje_alto - $porcentaje_medio }}" />
                                    @endif
                                @else
                                    <circle class="chart-segment segment-empty"
                                        cx="21" cy="21" r="15.9155"
                                        stroke-dasharray="100 0"
                                        stroke-dashoffset="25" />
                                @endif
                            </svg>
                            
                            <div class="chart-center">
                                <div class="chart-total">{{ $metricas['total_riesgos'] }}</div>
                                <div class="chart-label">Total</div>
                            </div>
                        </div>
                        
                        <div class="chart-legend">
                            <div class="legend-item">
                                <div class="legend-color alto"></div>
                                <div class="legend-info">
                                    <div class="legend-value">{{ $alto_count }}</div>
                                    <div class="legend-label">Alto Impacto</div>
                                </div>
                                <div class="legend-percentage">{{ $total > 0 ? round($porcentaje_alto) : 0 }}%</div>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color medio"></div>
                                <div class="legend-info">
                                    <div class="legend-value">{{ $medio_count }}</div>
                                    <div class="legend-label">Medio Impacto</div>
                                </div>
                                <div class="legend-percentage">{{ $total > 0 ? round($porcentaje_medio) : 0 }}%</div>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color bajo"></div>
                                <div class="legend-info">
                                    <div class="legend-value">{{ $bajo_count }}</div>
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
    .risk-chart-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
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
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.15));
    }

    .chart-background {
        fill: none;
        stroke: #f8f9fa;
        stroke-width: 8;
    }

    .chart-segment {
        fill: none;
        stroke-width: 8;
        stroke-linecap: butt;
        transition: all 0.8s ease;
        animation: drawSegment 1.5s ease-out forwards;
    }

    @keyframes drawSegment {
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

    .segment-empty {
        stroke: #bdc3c7;
        stroke-width: 8;
    }

    .chart-segment:hover {
        stroke-width: 10;
        filter: brightness(1.1);
        cursor: pointer;
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
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border: 2px solid #f8f9fa;
    }

    .chart-total {
        font-size: 20px;
        font-weight: 800;
        color: #2c3e50;
        line-height: 1;
        margin-bottom: 2px;
    }

    .chart-label {
        font-size: 9px;
        color: #7f8c8d;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .chart-legend {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 15px;
        background: #f8f9fa;
        border-radius: 8px;
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }

    .legend-item:hover {
        transform: translateX(5px);
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        background: white;
    }

    .legend-item:hover .legend-color {
        transform: scale(1.1);
    }

    .legend-color {
        width: 20px;
        height: 20px;
        border-radius: 6px;
        flex-shrink: 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
    }

    .legend-color.alto {
        background: #e74c3c;
        border-left: 4px solid #c0392b;
    }

    .legend-color.medio {
        background: #f39c12;
        border-left: 4px solid #e67e22;
    }

    .legend-color.bajo {
        background: #27ae60;
        border-left: 4px solid #229954;
    }

    .legend-info {
        flex: 1;
    }

    .legend-value {
        font-weight: 700;
        color: #2c3e50;
        font-size: 16px;
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
        font-size: 14px;
        background: white;
        padding: 4px 10px;
        border-radius: 6px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        min-width: 45px;
        text-align: center;
    }

    /* ESTILOS ORIGINALES DE ALERTAS */
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

    /* Quick Actions originales */
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

    @media (max-width: 768px) {
        .risk-chart-container {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .chart-svg {
            width: 180px;
            height: 180px;
        }
        
        .chart-center {
            width: 70px;
            height: 70px;
        }
        
        .chart-total {
            font-size: 18px;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const segments = document.querySelectorAll('.chart-segment');
        
        segments.forEach((segment, index) => {
            segment.style.animationDelay = `${index * 0.3}s`;
            
            segment.addEventListener('mouseenter', function() {
                this.style.strokeWidth = '10';
                this.style.filter = 'brightness(1.2)';
            });
            
            segment.addEventListener('mouseleave', function() {
                this.style.strokeWidth = '8';
                this.style.filter = 'brightness(1)';
            });
        });

        const legendItems = document.querySelectorAll('.legend-item');
        
        legendItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                const segmentType = this.querySelector('.legend-color').classList[1];
                const correspondingSegment = document.querySelector(`.segment-${segmentType}`);
                
                if (correspondingSegment) {
                    correspondingSegment.style.strokeWidth = '10';
                    correspondingSegment.style.filter = 'brightness(1.2)';
                }
            });
            
            item.addEventListener('mouseleave', function() {
                const segmentType = this.querySelector('.legend-color').classList[1];
                const correspondingSegment = document.querySelector(`.segment-${segmentType}`);
                
                if (correspondingSegment) {
                    correspondingSegment.style.strokeWidth = '8';
                    correspondingSegment.style.filter = 'brightness(1)';
                }
            });
        });
    });
</script>
@endsection