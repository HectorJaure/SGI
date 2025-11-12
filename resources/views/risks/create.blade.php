@extends('layouts.app')

@section('title', 'Nuevo Riesgo - Sistema SGSST')

@section('page-title', 'Nuevo Riesgo')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('risks.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="lugar" class="form-label">Lugar *</label>
                            <input list="lugares-list" id="lugar" name="lugar" 
                                   value="{{ old('lugar') }}" 
                                   placeholder="Selecciona o escribe un nuevo lugar"
                                   class="form-control" required autocomplete="on">
                            <datalist id="lugares-list">
                                @foreach($lugares as $lugar)
                                    <option value="{{ $lugar }}">{{ $lugar }}</option>
                                @endforeach
                            </datalist>
                            @error('lugar')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="actividad" class="form-label">Actividad *</label>
                            <input list="actividades-list" id="actividad" name="actividad" 
                                value="{{ old('actividad') }}" 
                                placeholder="Selecciona o escribe una nueva actividad"
                                class="form-control" required autocomplete="on">
                            <datalist id="actividades-list">
                                @foreach($actividades as $actividad)
                                    <option value="{{ $actividad }}">{{ $actividad }}</option>
                                @endforeach
                            </datalist>
                            @error('actividad')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div> <!-- Cierre correcto del row -->

                <div class="mb-3">
                    <label for="peligro" class="form-label">Descripción del Peligro *</label>
                    <textarea name="peligro" id="peligro" class="form-control" rows="3" required>{{ old('peligro') }}</textarea>
                    @error('peligro')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tipo_riesgo" class="form-label">Tipo de Riesgo *</label>
                            <select name="tipo_riesgo" id="tipo_riesgo" class="form-select" required>
                                <option value="">Selecciona el tipo</option>
                                <option value="Interno" {{ old('tipo_riesgo') == 'Interno' ? 'selected' : '' }}>Interno</option>
                                <option value="Externo" {{ old('tipo_riesgo') == 'Externo' ? 'selected' : '' }}>Externo</option>
                            </select>
                            @error('tipo_riesgo')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="clasificacion" class="form-label">Clasificación *</label>
                            <select name="clasificacion" id="clasificacion" class="form-select" required>
                                <option value="">Selecciona la clasificación</option>
                                <option value="Seguridad" {{ old('clasificacion') == 'Seguridad' ? 'selected' : '' }}>Seguridad</option>
                                <option value="Salud" {{ old('clasificacion') == 'Salud' ? 'selected' : '' }}>Salud</option>
                            </select>
                            @error('clasificacion')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-group">
                        <label for="otros_factores" class="form-label">Otros Factores</label>
                        <input type="text" 
                            id="otros_factores" 
                            name="otros_factores" 
                            class="form-control"
                            value="{{ old('otros_factores') }}"
                            placeholder="Ingrese otros factores relevantes">
                        <small class="form-text text-muted">Dejar vacío si no aplica</small>
                        @error('otros_factores')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Sección Evaluación del Riesgo -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Evaluación del Riesgo *</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tiempo_exposicion" class="form-label">Tiempo de Exposición *</label>
                                    <select name="tiempo_exposicion" id="tiempo_exposicion" class="form-select" required>
                                        <option value="1.0" {{ old('tiempo_exposicion') == '1.0' ? 'selected' : '' }}>Menor (No rutinaria)</option>
                                        <option value="5.0" {{ old('tiempo_exposicion') == '5.0' ? 'selected' : '' }}>Todo el tiempo (Rutinario)</option>
                                    </select>
                                    @error('tiempo_exposicion')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="personas_expuestas" class="form-label">No. de Personas Expuestas *</label>
                                    <select name="personas_expuestas" id="personas_expuestas" class="form-select" required>
                                        <option value="1.0" {{ old('personas_expuestas') == '1.0' ? 'selected' : '' }}>1 a 5 personas</option>
                                        <option value="2.0" {{ old('personas_expuestas') == '2.0' ? 'selected' : '' }}>6 a 10 personas</option>
                                        <option value="3.0" {{ old('personas_expuestas') == '3.0' ? 'selected' : '' }}>11 a 50 personas</option>
                                        <option value="4.0" {{ old('personas_expuestas') == '4.0' ? 'selected' : '' }}>51 a 500 personas</option>
                                        <option value="5.0" {{ old('personas_expuestas') == '5.0' ? 'selected' : '' }}>Más de 500 personas</option>
                                    </select>
                                    @error('personas_expuestas')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="probabilidad_ocurrencia" class="form-label">Probabilidad de Ocurrencia *</label>
                                    <select name="probabilidad_ocurrencia" id="probabilidad_ocurrencia" class="form-select" required>
                                        <option value="1.0" {{ old('probabilidad_ocurrencia') == '1.0' ? 'selected' : '' }}>Baja</option>
                                        <option value="3.0" {{ old('probabilidad_ocurrencia') == '3.0' ? 'selected' : '' }}>Mediana</option>
                                        <option value="5.0" {{ old('probabilidad_ocurrencia') == '5.0' ? 'selected' : '' }}>Alta</option>
                                    </select>
                                    @error('probabilidad_ocurrencia')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="consecuencia_personas" class="form-label">Consecuencia a Personas *</label>
                                    <select name="consecuencia_personas" id="consecuencia_personas" class="form-select" required>
                                        <option value="1.0" {{ old('consecuencia_personas') == '1.0' ? 'selected' : '' }}>Baja</option>
                                        <option value="3.0" {{ old('consecuencia_personas') == '3.0' ? 'selected' : '' }}>Mediana</option>
                                        <option value="5.0" {{ old('consecuencia_personas') == '5.0' ? 'selected' : '' }}>Alta</option>
                                    </select>
                                    @error('consecuencia_personas')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="consecuencia_infraestructura" class="form-label">Consecuencia a Infraestructura *</label>
                                    <select name="consecuencia_infraestructura" id="consecuencia_infraestructura" class="form-select" required>
                                        <option value="0.0" {{ old('consecuencia_infraestructura') == '0.0' ? 'selected' : '' }}>Extremadamente Baja</option>
                                        <option value="1.0" {{ old('consecuencia_infraestructura') == '1.0' ? 'selected' : '' }}>Baja</option>
                                        <option value="2.0" {{ old('consecuencia_infraestructura') == '2.0' ? 'selected' : '' }}>Mediana</option>
                                        <option value="3.0" {{ old('consecuencia_infraestructura') == '3.0' ? 'selected' : '' }}>Alta</option>
                                    </select>
                                    @error('consecuencia_infraestructura')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="form-group">
                                <label for="significancia_calculada" class="form-label">Significancia Calculada</label>
                                <input type="text" id="significancia_calculada" class="form-control" 
                                       value="0.0" readonly 
                                       style="background-color: #f8f9fa; font-weight: bold;">
                                <small class="form-text text-muted">Esta valor se calcula automáticamente</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('risks.matrix') }}" class="btn btn-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-success">Guardar Riesgo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .form-label {
        font-weight: 500;
        color: #4a5568;
        margin-bottom: 8px;
    }
    
    .card-header {
        font-weight: 600;
        color: #2c5282;
    }
    
    .form-select:focus, .form-control:focus {
        border-color: #4299e1;
        box-shadow: 0 0 0 0.2rem rgba(66, 153, 225, 0.25);
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

    .mb-3 {
        margin-bottom: 1rem;
        position: relative;
    }
</style>
@endsection

@section('scripts')
<script>
    function calcularSignificancia() {
        const tiempo = parseFloat(document.getElementById('tiempo_exposicion').value) || 0;
        const personas = parseFloat(document.getElementById('personas_expuestas').value) || 0;
        const probabilidad = parseFloat(document.getElementById('probabilidad_ocurrencia').value) || 0;
        const infraestructura = parseFloat(document.getElementById('consecuencia_infraestructura').value) || 0;
        const personasConsec = parseFloat(document.getElementById('consecuencia_personas').value) || 0;
        
        const probabilidadTotal = tiempo + personas + probabilidad;
        const consecuenciaTotal = infraestructura + personasConsec;
        const significancia = probabilidadTotal * consecuenciaTotal;
        
        // Mostrar resultado
        document.getElementById('significancia_calculada').value = significancia.toFixed(1);
        
        return significancia;
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

    // Validaciones del formulario de riesgos
    function validarFormularioRiesgo() {
        const formulario = document.querySelector('form');
        let valido = true;

        // Limpiar todos los errores previos
        formulario.querySelectorAll('.error-message').forEach(error => error.remove());
        formulario.querySelectorAll('.error').forEach(campo => campo.classList.remove('error'));

        // Validar campos requeridos
        const camposRequeridos = ['lugar', 'actividad', 'peligro', 'tipo_riesgo', 'clasificacion'];
        camposRequeridos.forEach(campoId => {
            const campo = document.getElementById(campoId);
            if (campo && !campo.value.trim()) {
                mostrarError(campo, 'Este campo es obligatorio');
                valido = false;
            }
        });

        // Validar longitud de campos de texto
        const campoPeligro = document.getElementById('peligro');
        if (campoPeligro && campoPeligro.value.length > 1000) {
            mostrarError(campoPeligro, 'No puede exceder 1000 caracteres');
            valido = false;
        }

        const campoOtrosFactores = document.getElementById('otros_factores');
        if (campoOtrosFactores && campoOtrosFactores.value.length > 255) {
            mostrarError(campoOtrosFactores, 'No puede exceder 255 caracteres');
            valido = false;
        }

        // Validar que los valores numéricos sean válidos
        const camposNumericos = [
            'tiempo_exposicion', 'personas_expuestas', 'probabilidad_ocurrencia',
            'consecuencia_personas', 'consecuencia_infraestructura'
        ];

        camposNumericos.forEach(campoId => {
            const campo = document.getElementById(campoId);
            if (campo && campo.value) {
                const valor = parseFloat(campo.value);
                if (isNaN(valor) || valor < 0) {
                    mostrarError(campo, 'Debe ser un número válido mayor o igual a 0');
                    valido = false;
                }
            }
        });

        // Validar que se haya calculado la significancia
        const significancia = calcularSignificancia();
        if (significancia === 0) {
            valido = false;
            alert('Debe completar todos los campos de evaluación para calcular la significancia');
        }

        return valido;
    }

    // Calcular significancia cuando cambien los valores
    document.addEventListener('DOMContentLoaded', function() {
        // Calcular significancia inicial
        calcularSignificancia();
        
        // Agregar event listeners a todos los selects
        document.querySelectorAll('#tiempo_exposicion, #personas_expuestas, #probabilidad_ocurrencia, #consecuencia_personas, #consecuencia_infraestructura').forEach(element => {
            element.addEventListener('change', calcularSignificancia);
        });

        // Agregar validación en tiempo real
        const campos = document.querySelectorAll('input, select, textarea');
        campos.forEach(campo => {
            campo.addEventListener('blur', function() {
                validarCampoRiesgo(this);
            });
        });

        // Prevenir envío del formulario si no es válido
        const formulario = document.querySelector('form');
        if (formulario) {
            formulario.addEventListener('submit', function(e) {
                if (!validarFormularioRiesgo()) {
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

    // Validación individual de campos para riesgos
    function validarCampoRiesgo(campo) {
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
            case 'peligro':
                if (valor.length > 1000) {
                    mostrarError(campo, 'No puede exceder 1000 caracteres');
                    return false;
                }
                break;
            case 'otros_factores':
                if (valor.length > 255) {
                    mostrarError(campo, 'No puede exceder 255 caracteres');
                    return false;
                }
                break;
        }

        return true;
    }
</script>
@endsection