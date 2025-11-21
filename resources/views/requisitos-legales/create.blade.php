@extends('layouts.app')

@section('title', 'Nuevo Requisito Legal')

@section('header-title', 'Nuevo Requisito Legal - Sistema SGSST')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('requisitos-legales.store') }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="categoria_norma" class="form-label">Categoría de Norma *</label>
                        <select name="categoria_norma" id="categoria_norma" class="form-select" required>
                            <option value="">Seleccionar categoría</option>
                            <option value="seguridad">Normas de Seguridad</option>
                            <option value="salud">Normas de Salud</option>
                            <option value="organizacion">Normas de Organización</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="norma" class="form-label">Norma *</label>
                            <input list="normas-list" id="norma" name="norma" 
                                value="{{ old('norma') }}" 
                                placeholder="Selecciona o escribe una nueva norma"
                                class="form-control" required autocomplete="on">
                            <datalist id="normas-list">
                                @isset($normasExistentes)
                                    @foreach($normasExistentes as $norma)
                                        <option value="{{ $norma }}">{{ $norma }}</option>
                                    @endforeach
                                @endisset
                            </datalist>
                            @error('norma')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="tipo_requisito" class="form-label">Tipo de Requisito *</label>
                            <input list="tipos-requisito-list" id="tipo_requisito" name="tipo_requisito" 
                                value="{{ old('tipo_requisito') }}" 
                                placeholder="Selecciona o escribe un nuevo tipo"
                                class="form-control" required autocomplete="on">
                            <datalist id="tipos-requisito-list">
                                @isset($tiposRequisitoExistentes)
                                    @foreach($tiposRequisitoExistentes as $tipo)
                                        <option value="{{ $tipo }}">{{ $tipo }}</option>
                                    @endforeach
                                @endisset
                            </datalist>
                            @error('tipo_requisito')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="numero_requisito" class="form-label">No. De Requisito *</label>
                        <input type="text" name="numero_requisito" id="numero_requisito" 
                               class="form-control" placeholder="Ej: 4.1, 6.1.2" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="titulo" class="form-label">Título *</label>
                        <input type="text" name="titulo" id="titulo" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción *</label>
                    <textarea name="descripcion" id="descripcion" class="form-control" rows="3" required></textarea>
                </div>

                <!-- Sección Cumplimiento -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Cumplimiento</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="cumplimiento" 
                                           id="cumplimiento_si" value="si" onchange="toggleCumplimiento()">
                                    <label class="form-check-label" for="cumplimiento_si">
                                        SI - Se cumple el requisito
                                    </label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="cumplimiento" 
                                           id="cumplimiento_no" value="no" onchange="toggleCumplimiento()">
                                    <label class="form-check-label" for="cumplimiento_no">
                                        NO - No se cumple el requisito
                                    </label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="cumplimiento" 
                                           id="cumplimiento_null" value="" onchange="toggleCumplimiento()" checked>
                                    <label class="form-check-label" for="cumplimiento_null">
                                        Sin evaluar
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div id="opcion_si" class="cumplimiento-option">
                                    <label for="evidencia" class="form-label">Evidencia</label>
                                    <textarea name="evidencia" id="evidencia" class="form-control" 
                                              rows="3" placeholder="Describa la evidencia del cumplimiento..."></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div id="opcion_no" class="cumplimiento-option">
                                    <label for="acciones_no" class="form-label">Acciones Requeridas</label>
                                    <textarea name="acciones_no" id="acciones_no" class="form-control" 
                                              rows="3" placeholder="Describa las acciones necesarias para el cumplimiento..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="peligro_asociado" class="form-label">Peligro Asociado</label>
                    <input type="text" name="peligro_asociado" id="peligro_asociado" class="form-control">
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="fecha_cumplimiento" class="form-label">Fecha de cumplimiento</label>
                        <input type="date" name="fecha_cumplimiento" id="fecha_cumplimiento" 
                               class="form-control">
                    </div>
                    
                    <div class="col-md-4">
                        <label for="responsables" class="form-label">Responsable(s)</label>
                        <input type="text" name="responsables" id="responsables" class="form-control">
                    </div>
                    
                    <div class="col-md-4">
                        <label for="frecuencia_control" class="form-label">Frecuencia del control</label>
                        <select name="frecuencia_control" id="frecuencia_control" class="form-select">
                            <option value="">Seleccionar frecuencia</option>
                            <option value="Diaria">Diaria</option>
                            <option value="Semanal">Semanal</option>
                            <option value="Quincenal">Quincenal</option>
                            <option value="Mensual">Mensual</option>
                            <option value="Bimestral">Bimestral</option>
                            <option value="Trimestral">Trimestral</option>
                            <option value="Semestral">Semestral</option>
                            <option value="Anual">Anual</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="responsable_control" class="form-label">Responsable(s) del control</label>
                    <input type="text" name="responsable_control" id="responsable_control" class="form-control">
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('requisitos-legales.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-success">Guardar Requisito</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .cumplimiento-option {
        padding: 15px;
        border: 2px solid #e2e8f0;
        border-radius: 6px;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }
    
    .form-check-input:checked {
        background-color: #4299e1;
        border-color: #4299e1;
    }
    
    .form-label {
        font-weight: 500;
        color: #4a5568;
        margin-bottom: 8px;
    }
    
    .card-header {
        font-weight: 600;
        color: #2c5282;
    }

    /* Estilos para errores */
    .error {
        border-color: #e53e3e !important;
        background-color: #fed7d7 !important;
    }

    .error-message {
        color: #e53e3e;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
        font-weight: 500;
    }

    .form-group {
        margin-bottom: 1rem;
        position: relative;
    }
</style>
@endsection

@section('scripts')
<script>
    function toggleCumplimiento() {
        const cumplimientoSi = document.getElementById('cumplimiento_si');
        const cumplimientoNo = document.getElementById('cumplimiento_no');
        const cumplimientoNull = document.getElementById('cumplimiento_null');
        const opcionSi = document.getElementById('opcion_si');
        const opcionNo = document.getElementById('opcion_no');
        
        if (cumplimientoSi.checked) {
            opcionSi.style.opacity = '1';
            opcionNo.style.opacity = '0.6';
        } else if (cumplimientoNo.checked) {
            opcionSi.style.opacity = '0.6';
            opcionNo.style.opacity = '1';
        } else {
            opcionSi.style.opacity = '0.6';
            opcionNo.style.opacity = '0.6';
        }
    }

    // Función para mostrar mensajes de error debajo de cada campo
    function mostrarError(campo, mensaje) {
        // Remover error anterior
        removerError(campo);
        
        // Agregar clase de error al campo
        campo.classList.add('error');
        
        // Crear elemento de mensaje de error
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.id = `error-${campo.id}`;
        errorDiv.textContent = mensaje;
        
        // Insertar después del campo
        campo.parentNode.appendChild(errorDiv);
    }

    function removerError(campo) {
        campo.classList.remove('error');
        const errorExistente = document.getElementById(`error-${campo.id}`);
        if (errorExistente) {
            errorExistente.remove();
        }
    }

    // Validaciones del formulario
    function validarFormularioRequisito() {
        const formulario = document.querySelector('form');
        const camposRequeridos = formulario.querySelectorAll('[required]');
        let valido = true;

        // Limpiar todos los errores previos
        formulario.querySelectorAll('.error-message').forEach(error => error.remove());
        formulario.querySelectorAll('.error').forEach(campo => campo.classList.remove('error'));

        // Validar campos requeridos
        camposRequeridos.forEach(campo => {
            if (!campo.value.trim()) {
                mostrarError(campo, 'Este campo es obligatorio');
                valido = false;
            }
        });

        // Validar número de requisito (formato: número.punto.número)
        const numeroRequisito = document.getElementById('numero_requisito');
        if (numeroRequisito.value && !/^[\d\.]+$/.test(numeroRequisito.value)) {
            mostrarError(numeroRequisito, 'Solo puede contener números y puntos');
            valido = false;
        }

        // Validar fecha de cumplimiento (no puede ser anterior a hoy si se proporciona)
        const fechaCumplimiento = document.getElementById('fecha_cumplimiento');
        if (fechaCumplimiento.value) {
            const hoy = new Date().toISOString().split('T')[0];
            if (fechaCumplimiento.value < hoy) {
                mostrarError(fechaCumplimiento, 'No puede ser anterior a la fecha actual');
                valido = false;
            }
        }

        return valido;
    }

    // Inicializar el estado al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        toggleCumplimiento();
        
        // Agregar estilos para las opciones de cumplimiento
        const opcionSi = document.getElementById('opcion_si');
        const opcionNo = document.getElementById('opcion_no');
        
        if (opcionSi) opcionSi.style.transition = 'opacity 0.3s ease';
        if (opcionNo) opcionNo.style.transition = 'opacity 0.3s ease';

        // Agregar validación en tiempo real
        const campos = document.querySelectorAll('input, select, textarea');
        campos.forEach(campo => {
            campo.addEventListener('blur', function() {
                validarCampo(this);
            });
            
            // Validación inmediata para campos de texto
            if (campo.type === 'text' || campo.type === 'textarea') {
                campo.addEventListener('input', function() {
                    validarCampo(this);
                });
            }
        });

        // Prevenir envío del formulario si no es válido
        const formulario = document.querySelector('form');
        if (formulario) {
            formulario.addEventListener('submit', function(e) {
                if (!validarFormularioRequisito()) {
                    e.preventDefault();
                    // Hacer scroll al primer error
                    const primerError = formulario.querySelector('.error');
                    if (primerError) {
                        primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
        }
    });

    // Validación individual de campos
    function validarCampo(campo) {
        const valor = campo.value.trim();
        
        // Limpiar error previo
        removerError(campo);
        
        // Validar campo requerido
        if (campo.hasAttribute('required') && !valor) {
            mostrarError(campo, 'Este campo es obligatorio');
            return false;
        }

        // Validaciones específicas por tipo de campo
        switch(campo.id) {
            case 'numero_requisito':
                if (valor && !/^[\d\.]+$/.test(valor)) {
                    mostrarError(campo, 'Solo puede contener números y puntos');
                    return false;
                }
                break;
            case 'fecha_cumplimiento':
                if (valor) {
                    const hoy = new Date().toISOString().split('T')[0];
                    if (valor < hoy) {
                        mostrarError(campo, 'No puede ser anterior a hoy');
                        return false;
                    }
                }
                break;
        }

        return true;
    }
</script>
@endsection