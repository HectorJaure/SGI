@extends('layouts.app')

@section('title', 'Editar Requisito Legal')
@section('page-title', 'Editar Requisito Legal')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('requisitos-legales.update', $requisito->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="norma" class="form-label">Norma *</label>
                        <select name="norma" id="norma" class="form-select" required>
                            <option value="">Seleccionar norma</option>
                            <option value="ISO 45001:2018" {{ $requisito->norma == 'ISO 45001:2018' ? 'selected' : '' }}>ISO 45001:2018</option>
                            <option value="NOM-030-STPS" {{ $requisito->norma == 'NOM-030-STPS' ? 'selected' : '' }}>NOM-030-STPS</option>
                            <option value="NOM-035-STPS" {{ $requisito->norma == 'NOM-035-STPS' ? 'selected' : '' }}>NOM-035-STPS</option>
                            <option value="LFT" {{ $requisito->norma == 'LFT' ? 'selected' : '' }}>Ley Federal del Trabajo</option>
                            <option value="Reglamento Federal" {{ $requisito->norma == 'Reglamento Federal' ? 'selected' : '' }}>Reglamento Federal de Seguridad y Salud</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="tipo_requisito" class="form-label">Tipo de Requisito *</label>
                        <select name="tipo_requisito" id="tipo_requisito" class="form-select" required>
                            <option value="">Seleccionar tipo</option>
                            <option value="Requisito General" {{ $requisito->tipo_requisito == 'Requisito General' ? 'selected' : '' }}>Requisito General</option>
                            <option value="Requisito de Proceso" {{ $requisito->tipo_requisito == 'Requisito de Proceso' ? 'selected' : '' }}>Requisito de Proceso</option>
                            <option value="Requisito Legal" {{ $requisito->tipo_requisito == 'Requisito Legal' ? 'selected' : '' }}>Requisito Legal</option>
                            <option value="Requisito Documental" {{ $requisito->tipo_requisito == 'Requisito Documental' ? 'selected' : '' }}>Requisito Documental</option>
                            <option value="Requisito de Control" {{ $requisito->tipo_requisito == 'Requisito de Control' ? 'selected' : '' }}>Requisito de Control</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="numero_requisito" class="form-label">No. De Requisito *</label>
                        <input type="text" name="numero_requisito" id="numero_requisito" 
                               class="form-control" placeholder="Ej: 4.1, 6.1.2" 
                               value="{{ old('numero_requisito', $requisito->numero_requisito) }}" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="titulo" class="form-label">Título *</label>
                        <input type="text" name="titulo" id="titulo" class="form-control" 
                               value="{{ old('titulo', $requisito->titulo) }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción *</label>
                    <textarea name="descripcion" id="descripcion" class="form-control" rows="3" required>{{ old('descripcion', $requisito->descripcion) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="peligro_asociado" class="form-label">Peligro Asociado *</label>
                    <input type="text" name="peligro_asociado" id="peligro_asociado" class="form-control" 
                           value="{{ old('peligro_asociado', $requisito->peligro_asociado) }}" required>
                </div>

                <!-- Sección Cumplimiento -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Cumplimiento *</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="cumplimiento" 
                                           id="cumplimiento_si" value="si" 
                                           {{ $requisito->cumplimiento == 'si' ? 'checked' : '' }} 
                                           onchange="toggleCumplimiento()">
                                    <label class="form-check-label" for="cumplimiento_si">
                                        SI - Se cumple el requisito
                                    </label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="cumplimiento" 
                                           id="cumplimiento_no" value="no" 
                                           {{ $requisito->cumplimiento == 'no' ? 'checked' : '' }} 
                                           onchange="toggleCumplimiento()">
                                    <label class="form-check-label" for="cumplimiento_no">
                                        NO - No se cumple el requisito
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div id="opcion_si" class="cumplimiento-option">
                                    <label for="evidencia" class="form-label">Evidencia</label>
                                    <textarea name="evidencia" id="evidencia" class="form-control" 
                                              rows="3" placeholder="Describa la evidencia del cumplimiento...">{{ old('evidencia', $requisito->evidencia) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div id="opcion_no" class="cumplimiento-option">
                                    <label for="acciones_no" class="form-label">Acciones Requeridas</label>
                                    <textarea name="acciones_no" id="acciones_no" class="form-control" 
                                              rows="3" placeholder="Describa las acciones necesarias para el cumplimiento...">{{ old('acciones_no', $requisito->acciones_no) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="fecha_cumplimiento" class="form-label">Fecha de cumplimiento *</label>
                        <input type="date" name="fecha_cumplimiento" id="fecha_cumplimiento" 
                               class="form-control" 
                               value="{{ old('fecha_cumplimiento', $requisito->fecha_cumplimiento->format('Y-m-d')) }}" 
                               required>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="responsables" class="form-label">Responsable(s) *</label>
                        <input type="text" name="responsables" id="responsables" class="form-control" 
                               value="{{ old('responsables', $requisito->responsables) }}" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="frecuencia_control" class="form-label">Frecuencia del control *</label>
                        <select name="frecuencia_control" id="frecuencia_control" class="form-select" required>
                            <option value="">Seleccionar frecuencia</option>
                            <option value="Diaria" {{ $requisito->frecuencia_control == 'Diaria' ? 'selected' : '' }}>Diaria</option>
                            <option value="Semanal" {{ $requisito->frecuencia_control == 'Semanal' ? 'selected' : '' }}>Semanal</option>
                            <option value="Quincenal" {{ $requisito->frecuencia_control == 'Quincenal' ? 'selected' : '' }}>Quincenal</option>
                            <option value="Mensual" {{ $requisito->frecuencia_control == 'Mensual' ? 'selected' : '' }}>Mensual</option>
                            <option value="Bimestral" {{ $requisito->frecuencia_control == 'Bimestral' ? 'selected' : '' }}>Bimestral</option>
                            <option value="Trimestral" {{ $requisito->frecuencia_control == 'Trimestral' ? 'selected' : '' }}>Trimestral</option>
                            <option value="Semestral" {{ $requisito->frecuencia_control == 'Semestral' ? 'selected' : '' }}>Semestral</option>
                            <option value="Anual" {{ $requisito->frecuencia_control == 'Anual' ? 'selected' : '' }}>Anual</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="responsable_control" class="form-label">Responsable(s) del control *</label>
                    <input type="text" name="responsable_control" id="responsable_control" class="form-control" 
                           value="{{ old('responsable_control', $requisito->responsable_control) }}" required>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('requisitos-legales.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Actualizar Requisito
                    </button>
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
        const opcionSi = document.getElementById('opcion_si');
        const opcionNo = document.getElementById('opcion_no');
        
        if (cumplimientoSi.checked) {
            opcionSi.style.opacity = '1';
            opcionNo.style.opacity = '0.6';
            document.getElementById('evidencia').required = true;
            document.getElementById('acciones_no').required = false;
        } else if (cumplimientoNo.checked) {
            opcionSi.style.opacity = '0.6';
            opcionNo.style.opacity = '1';
            document.getElementById('evidencia').required = false;
            document.getElementById('acciones_no').required = true;
        } else {
            opcionSi.style.opacity = '0.6';
            opcionNo.style.opacity = '0.6';
            document.getElementById('evidencia').required = false;
            document.getElementById('acciones_no').required = false;
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

        // Validar fecha de cumplimiento (no puede ser anterior a hoy)
        const fechaCumplimiento = document.getElementById('fecha_cumplimiento');
        const hoy = new Date().toISOString().split('T')[0];
        if (fechaCumplimiento.value && fechaCumplimiento.value < hoy) {
            mostrarError(fechaCumplimiento, 'No puede ser anterior a la fecha actual');
            valido = false;
        }

        // Validar que se haya seleccionado cumplimiento
        const cumplimientoSeleccionado = document.querySelector('input[name="cumplimiento"]:checked');
        if (!cumplimientoSeleccionado) {
            alert('Debe seleccionar si cumple o no el requisito');
            valido = false;
        }

        // Validar longitud máxima de campos
        const validacionesLongitud = [
            { campo: 'norma', max: 255 },
            { campo: 'titulo', max: 255 },
            { campo: 'tipo_requisito', max: 255 },
            { campo: 'numero_requisito', max: 50 },
            { campo: 'peligro_asociado', max: 255 },
            { campo: 'responsables', max: 255 },
            { campo: 'frecuencia_control', max: 100 },
            { campo: 'responsable_control', max: 255 }
        ];

        validacionesLongitud.forEach(validacion => {
            const campo = document.getElementById(validacion.campo);
            if (campo && campo.value.length > validacion.max) {
                mostrarError(campo, `No puede exceder ${validacion.max} caracteres`);
                valido = false;
            }
        });

        return valido;
    }

    // Inicializar el estado al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        toggleCumplimiento();
        
        // Establecer fecha actual por defecto
        const fechaCumplimiento = document.getElementById('fecha_cumplimiento');
        if (fechaCumplimiento && !fechaCumplimiento.value) {
            const today = new Date().toISOString().split('T')[0];
            fechaCumplimiento.value = today;
        }
        
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
            case 'email':
                if (valor && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valor)) {
                    mostrarError(campo, 'Formato de email inválido');
                    return false;
                }
                break;
        }

        // Validar longitud máxima
        const maxLength = campo.getAttribute('maxlength');
        if (maxLength && valor.length > parseInt(maxLength)) {
            mostrarError(campo, `Máximo ${maxLength} caracteres`);
            return false;
        }

        return true;
    }
</script>
@endsection