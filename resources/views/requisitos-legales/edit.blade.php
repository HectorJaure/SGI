@extends('layouts.app')

@section('title', 'Editar Requisito Legal')

@section('header-title', 'Editar Requisito Legal - Sistema SGSST')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('requisitos-legales.update', $requisito->id) }}" method="POST" id="requisitoForm">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="categoria_norma" class="form-label">Categoría de Norma *</label>
                        <select name="categoria_norma" id="categoria_norma" class="form-select" required>
                            <option value="">Seleccionar categoría</option>
                            <option value="seguridad" {{ $requisito->categoria_norma == 'seguridad' ? 'selected' : '' }}>Normas de Seguridad</option>
                            <option value="salud" {{ $requisito->categoria_norma == 'salud' ? 'selected' : '' }}>Normas de Salud</option>
                            <option value="organizacion" {{ $requisito->categoria_norma == 'organizacion' ? 'selected' : '' }}>Normas de Organización</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="norma" class="form-label">Norma *</label>
                            <input list="normas-list" id="norma" name="norma" 
                                value="{{ old('norma', $requisito->norma) }}" 
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
                                value="{{ old('tipo_requisito', $requisito->tipo_requisito) }}" 
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
                                           id="cumplimiento_si" value="si" 
                                           {{ old('cumplimiento', $requisito->cumplimiento) == 'si' ? 'checked' : '' }} 
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
                                           {{ old('cumplimiento', $requisito->cumplimiento) == 'no' ? 'checked' : '' }} 
                                           onchange="toggleCumplimiento()">
                                    <label class="form-check-label" for="cumplimiento_no">
                                        NO - No se cumple el requisito
                                    </label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="cumplimiento" 
                                           id="cumplimiento_null" value="" 
                                           {{ old('cumplimiento', $requisito->cumplimiento) == '' || is_null(old('cumplimiento', $requisito->cumplimiento)) ? 'checked' : '' }} 
                                           onchange="toggleCumplimiento()">
                                    <label class="form-check-label" for="cumplimiento_null">
                                        SIN INCIDENTES
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
                                    <small class="text-muted">Este campo se habilita cuando selecciona "SI"</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div id="opcion_no" class="cumplimiento-option">
                                    <label for="acciones_no" class="form-label">Acciones Requeridas</label>
                                    <textarea name="acciones_no" id="acciones_no" class="form-control" 
                                              rows="3" placeholder="Describa las acciones necesarias para el cumplimiento...">{{ old('acciones_no', $requisito->acciones_no) }}</textarea>
                                    <small class="text-muted">Este campo se habilita cuando selecciona "NO"</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campos opcionales que se bloquean en "Sin evaluar" -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Información Adicional</h6>
                        <small class="text-muted">Solo habilitados y obligatorios cuando hay algun incidente relacionado</small>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="peligro_asociado" class="form-label">Peligro Asociado</label>
                            <input type="text" name="peligro_asociado" id="peligro_asociado" class="form-control" 
                                   value="{{ old('peligro_asociado', $requisito->peligro_asociado) }}"
                                   placeholder="Describa el peligro asociado al requisito">
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="fecha_cumplimiento" class="form-label">Fecha de cumplimiento</label>
                                <input type="date" name="fecha_cumplimiento" id="fecha_cumplimiento" 
                                       class="form-control" 
                                       value="{{ old('fecha_cumplimiento', $requisito->fecha_cumplimiento ? $requisito->fecha_cumplimiento->format('Y-m-d') : '') }}">
                            </div>
                            
                            <div class="col-md-4">
                                <label for="responsables" class="form-label">Responsable(s)</label>
                                <input type="text" name="responsables" id="responsables" class="form-control" 
                                       value="{{ old('responsables', $requisito->responsables) }}"
                                       placeholder="Nombre del responsable">
                            </div>
                            
                            <div class="col-md-4">
                                <label for="frecuencia_control" class="form-label">Frecuencia del control</label>
                                <select name="frecuencia_control" id="frecuencia_control" class="form-select">
                                    <option value="">Seleccionar frecuencia</option>
                                    <option value="Diaria" {{ old('frecuencia_control', $requisito->frecuencia_control) == 'Diaria' ? 'selected' : '' }}>Diaria</option>
                                    <option value="Semanal" {{ old('frecuencia_control', $requisito->frecuencia_control) == 'Semanal' ? 'selected' : '' }}>Semanal</option>
                                    <option value="Quincenal" {{ old('frecuencia_control', $requisito->frecuencia_control) == 'Quincenal' ? 'selected' : '' }}>Quincenal</option>
                                    <option value="Mensual" {{ old('frecuencia_control', $requisito->frecuencia_control) == 'Mensual' ? 'selected' : '' }}>Mensual</option>
                                    <option value="Bimestral" {{ old('frecuencia_control', $requisito->frecuencia_control) == 'Bimestral' ? 'selected' : '' }}>Bimestral</option>
                                    <option value="Trimestral" {{ old('frecuencia_control', $requisito->frecuencia_control) == 'Trimestral' ? 'selected' : '' }}>Trimestral</option>
                                    <option value="Semestral" {{ old('frecuencia_control', $requisito->frecuencia_control) == 'Semestral' ? 'selected' : '' }}>Semestral</option>
                                    <option value="Anual" {{ old('frecuencia_control', $requisito->frecuencia_control) == 'Anual' ? 'selected' : '' }}>Anual</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="responsable_control" class="form-label">Responsable(s) del control</label>
                            <input type="text" name="responsable_control" id="responsable_control" class="form-control" 
                                   value="{{ old('responsable_control', $requisito->responsable_control) }}"
                                   placeholder="Nombre del responsable del control">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('requisitos-legales.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-success" id="btnActualizar">
                        <i class="fas fa-save"></i> Actualizar Requisito
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmación para "Sin evaluar" -->
<div id="modalConfirmacionSinEvaluar" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle me-2 text-warning"></i>Confirmar Cambio a "Sin evaluar"</h3>
            <span class="close" onclick="cerrarModalConfirmacion()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>¿Está seguro de cambiar el estado a "Sin evaluar"?</strong>
            </div>
            <p>Al cambiar a "Sin evaluar", se eliminarán los siguientes datos:</p>
            <ul id="listaDatosEliminar" class="mb-3">
                <!-- Los datos a eliminar se llenarán dinámicamente -->
            </ul>
            <p class="text-muted small">Esta acción no se puede deshacer.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary me-2" onclick="cerrarModalConfirmacion()">
                <i class="fas fa-times me-2"></i>Cancelar
            </button>
            <button type="button" class="btn btn-success" onclick="confirmarEnvio()">
                <i class="fas fa-check me-2"></i>Sí, Cambiar a "Sin evaluar"
            </button>
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

    /* Estilos para campos deshabilitados */
    .disabled-field {
        background-color: #f7fafc !important;
        border-color: #e2e8f0 !important;
        color: #a0aec0 !important;
        cursor: not-allowed !important;
    }

    .disabled-field::placeholder {
        color: #cbd5e0 !important;
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

    .text-muted {
        font-size: 0.8rem;
        margin-top: 4px;
        display: block;
    }

    /* Estilos para el modal de confirmación */
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

    #listaDatosEliminar {
        list-style-type: disc;
        margin-left: 20px;
    }

    #listaDatosEliminar li {
        margin-bottom: 5px;
        color: #e53e3e;
        font-weight: 500;
    }

    .text-danger {
        color: #e53e3e;
        font-weight: bold;
    }
</style>
@endsection

@section('scripts')
<script>
    // Variable para controlar el envío del formulario
    let formularioConfirmado = false;

    function toggleCumplimiento() {
        const cumplimientoSi = document.getElementById('cumplimiento_si');
        const cumplimientoNo = document.getElementById('cumplimiento_no');
        const cumplimientoNull = document.getElementById('cumplimiento_null');
        const opcionSi = document.getElementById('opcion_si');
        const opcionNo = document.getElementById('opcion_no');
        
        // Obtener todos los campos de información adicional
        const camposInformacionAdicional = [
            'peligro_asociado', 'fecha_cumplimiento', 'responsables', 
            'frecuencia_control', 'responsable_control'
        ];
        
        if (cumplimientoSi.checked) {
            // HABILITAR campos para "SI"
            opcionSi.style.opacity = '1';
            opcionNo.style.opacity = '0.6';
            document.getElementById('evidencia').disabled = false;
            document.getElementById('acciones_no').disabled = true;
            
            // HABILITAR campos de información adicional y hacerlos requeridos
            camposInformacionAdicional.forEach(campo => {
                const elemento = document.getElementById(campo);
                elemento.disabled = false;
                elemento.classList.remove('disabled-field');
                elemento.required = true; // Hacer requerido
            });
            
        } else if (cumplimientoNo.checked) {
            // HABILITAR campos para "NO"
            opcionSi.style.opacity = '0.6';
            opcionNo.style.opacity = '1';
            document.getElementById('evidencia').disabled = true;
            document.getElementById('acciones_no').disabled = false;
            
            // HABILITAR campos de información adicional y hacerlos requeridos
            camposInformacionAdicional.forEach(campo => {
                const elemento = document.getElementById(campo);
                elemento.disabled = false;
                elemento.classList.remove('disabled-field');
                elemento.required = true; // Hacer requerido
            });
            
        } else {
            // DESHABILITAR campos para "Sin evaluar" y quitar requerido
            opcionSi.style.opacity = '0.6';
            opcionNo.style.opacity = '0.6';
            document.getElementById('evidencia').disabled = true;
            document.getElementById('acciones_no').disabled = true;
            
            // DESHABILITAR campos de información adicional y quitar requerido
            camposInformacionAdicional.forEach(campo => {
                const elemento = document.getElementById(campo);
                elemento.disabled = true;
                elemento.classList.add('disabled-field');
                elemento.required = false; // Quitar requerido
            });
        }
    }

    // Función para verificar si hay datos que se eliminarán
    function hayDatosParaEliminar() {
        const camposOpcionales = [
            { id: 'evidencia', nombre: 'Evidencia' },
            { id: 'acciones_no', nombre: 'Acciones requeridas' },
            { id: 'peligro_asociado', nombre: 'Peligro asociado' },
            { id: 'fecha_cumplimiento', nombre: 'Fecha de cumplimiento' },
            { id: 'responsables', nombre: 'Responsables' },
            { id: 'frecuencia_control', nombre: 'Frecuencia de control' },
            { id: 'responsable_control', nombre: 'Responsable de control' }
        ];

        const datosConValor = [];

        camposOpcionales.forEach(campo => {
            const elemento = document.getElementById(campo.id);
            if (elemento && elemento.value.trim() !== '') {
                datosConValor.push(campo.nombre);
            }
        });

        return datosConValor;
    }

    // Función para preparar el envío del formulario (limpiar datos)
    function prepararEnvioFormulario() {
        const cumplimientoNull = document.getElementById('cumplimiento_null');
        
        if (cumplimientoNull.checked) {
            const formulario = document.getElementById('requisitoForm');
            
            // Campos que deben limpiarse cuando es "Sin evaluar"
            const camposALimpiar = [
                'evidencia', 'acciones_no', 'peligro_asociado', 
                'fecha_cumplimiento', 'responsables', 'frecuencia_control', 
                'responsable_control'
            ];
            
            // Remover campos hidden previos si existen
            document.querySelectorAll('input[type="hidden"][name^="force_clean_"]').forEach(el => el.remove());
            
            // Agregar campos hidden con valores vacíos para SOBREESCRIBIR los valores existentes
            camposALimpiar.forEach(campo => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = campo;
                hiddenInput.value = '';
                hiddenInput.className = 'force-clean-field';
                formulario.appendChild(hiddenInput);
            });
        }
    }

    // Mostrar modal de confirmación
    function mostrarModalConfirmacion(datosAEliminar) {
        const lista = document.getElementById('listaDatosEliminar');
        lista.innerHTML = '';
        
        datosAEliminar.forEach(dato => {
            const li = document.createElement('li');
            li.textContent = dato;
            lista.appendChild(li);
        });
        
        document.getElementById('modalConfirmacionSinEvaluar').style.display = 'block';
    }

    // Cerrar modal de confirmación
    function cerrarModalConfirmacion() {
        document.getElementById('modalConfirmacionSinEvaluar').style.display = 'none';
        formularioConfirmado = false;
    }

    // Confirmar el envío desde el modal
    function confirmarEnvio() {
        formularioConfirmado = true;
        prepararEnvioFormulario();
        document.getElementById('requisitoForm').submit();
    }

    // Función para mostrar mensajes de error
    function mostrarError(campo, mensaje) {
        removerError(campo);
        campo.classList.add('error');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.id = `error-${campo.id}`;
        errorDiv.textContent = mensaje;
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
        const formulario = document.getElementById('requisitoForm');
        const camposRequeridos = formulario.querySelectorAll('[required]');
        let valido = true;

        // Limpiar todos los errores previos
        formulario.querySelectorAll('.error-message').forEach(error => error.remove());
        formulario.querySelectorAll('.error').forEach(campo => campo.classList.remove('error'));

        // Validar campos requeridos (incluyendo los que se activan dinámicamente)
        camposRequeridos.forEach(campo => {
            if (!campo.disabled && !campo.value.trim()) {
                mostrarError(campo, 'Este campo es obligatorio');
                valido = false;
            }
        });

        // Validar campos condicionales según cumplimiento
        const cumplimientoSi = document.getElementById('cumplimiento_si').checked;
        const cumplimientoNo = document.getElementById('cumplimiento_no').checked;

        if (cumplimientoSi) {
            const evidencia = document.getElementById('evidencia');
            if (!evidencia.value.trim()) {
                mostrarError(evidencia, 'La evidencia es obligatoria cuando el requisito se cumple');
                valido = false;
            }
        }

        if (cumplimientoNo) {
            const accionesNo = document.getElementById('acciones_no');
            if (!accionesNo.value.trim()) {
                mostrarError(accionesNo, 'Las acciones requeridas son obligatorias cuando el requisito no se cumple');
                valido = false;
            }
        }

        // Validar número de requisito
        const numeroRequisito = document.getElementById('numero_requisito');
        if (numeroRequisito.value && !/^[\d\.]+$/.test(numeroRequisito.value)) {
            mostrarError(numeroRequisito, 'Solo puede contener números y puntos');
            valido = false;
        }

        // Validar fecha de cumplimiento
        const fechaCumplimiento = document.getElementById('fecha_cumplimiento');
        if (fechaCumplimiento.value && !fechaCumplimiento.disabled) {
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
        
        const opcionSi = document.getElementById('opcion_si');
        const opcionNo = document.getElementById('opcion_no');
        
        if (opcionSi) opcionSi.style.transition = 'opacity 0.3s ease';
        if (opcionNo) opcionNo.style.transition = 'opacity 0.3s ease';

        // Agregar validación en tiempo real
        const campos = document.querySelectorAll('input, select, textarea');
        campos.forEach(campo => {
            campo.addEventListener('blur', function() {
                if (!this.disabled) {
                    validarCampo(this);
                }
            });
            
            if ((campo.type === 'text' || campo.type === 'textarea') && !campo.disabled) {
                campo.addEventListener('input', function() {
                    validarCampo(this);
                });
            }
        });

        // Interceptar el envío del formulario
        const formulario = document.getElementById('requisitoForm');
        if (formulario) {
            formulario.addEventListener('submit', function(e) {
                // Si ya fue confirmado, permitir el envío
                if (formularioConfirmado) {
                    return true;
                }

                // Prevenir envío normal
                e.preventDefault();

                // Validar formulario
                if (!validarFormularioRequisito()) {
                    const primerError = formulario.querySelector('.error');
                    if (primerError) {
                        primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    return;
                }

                // Verificar si está cambiando a "Sin evaluar" y hay datos que se eliminarán
                const cumplimientoNull = document.getElementById('cumplimiento_null');
                if (cumplimientoNull.checked) {
                    const datosAEliminar = hayDatosParaEliminar();
                    
                    if (datosAEliminar.length > 0) {
                        // Mostrar modal de confirmación
                        mostrarModalConfirmacion(datosAEliminar);
                        return;
                    }
                }
                
                // Si no hay datos que eliminar o no es "Sin evaluar", enviar directamente
                formulario.submit();
            });
        }
    });

    // Validación individual de campos
    function validarCampo(campo) {
        if (campo.disabled) return true;
        
        const valor = campo.value.trim();
        removerError(campo);
        
        if (campo.hasAttribute('required') && !valor) {
            mostrarError(campo, 'Este campo es obligatorio');
            return false;
        }

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

    // Cerrar modal al hacer clic fuera
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('modalConfirmacionSinEvaluar');
        if (event.target === modal) {
            cerrarModalConfirmacion();
        }
    });
</script>
@endsection