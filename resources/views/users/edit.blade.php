@extends('layouts.app')

@section('title', 'Editar Usuario - Sistema SGSST')

@section('header-title', 'Editar Usuario')

@section('content')
<div class="container">
    <div class="form-body">
        <!-- Mensajes del sistema -->
        @if(session('success'))
            <div class="message success">
                {!! session('success') !!}
            </div>
        @endif

        @if($errors->any())
            <div class="message error">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('users.update', $user) }}" id="form-editar-usuario">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <!-- Información Personal -->
                <div class="form-group form-full-width">
                    <h3 style="color: #2c3e50; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #3498db;">Información Personal</h3>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Nombre Completo <span class="required">*</span>
                    </label>
                    <input type="text" name="nombre" class="form-input @error('nombre') error @enderror" 
                           placeholder="Ej: Juan Pérez García" required
                           value="{{ old('nombre', $user->nombre) }}">
                    @error('nombre')
                        <div class="form-help error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Username <span class="required">*</span>
                    </label>
                    <input type="text" name="username" class="form-input @error('username') error @enderror" 
                           placeholder="Ej: juan.perez" required
                           value="{{ old('username', $user->username) }}">
                    <div class="form-help">Se usará para iniciar sesión</div>
                    @error('username')
                        <div class="form-help error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Correo Electrónico <span class="required">*</span>
                    </label>
                    <input type="email" name="email" class="form-input @error('email') error @enderror" 
                           placeholder="Ej: usuario@itsn.edu.mx" required
                           value="{{ old('email', $user->email) }}">
                    <div class="form-help">El usuario usará este email para recuperar la contraseña</div>
                    @error('email')
                        <div class="form-help error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Rol del Usuario <span class="required">*</span>
                    </label>
                    <select name="rol" class="form-select @error('rol') error @enderror" required>
                        <option value="Usuario" {{ old('rol', $user->rol) === 'Usuario' ? 'selected' : '' }}>Usuario</option>
                        <option value="Administrador" {{ old('rol', $user->rol) === 'Administrador' ? 'selected' : '' }}>Administrador</option>
                    </select>
                    @error('rol')
                        <div class="form-help error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Departamento</label>
                    <input list="departamentos-list" id="departamento" name="departamento" 
                        value="{{ old('departamento', $user->departamento) }}" 
                        placeholder="Selecciona o escribe un nuevo departamento"
                        class="form-input @error('departamento') error @enderror" autocomplete="on">
                    <datalist id="departamentos-list">
                        @foreach($departamentos as $departamento)
                            <option value="{{ $departamento }}">{{ $departamento }}</option>
                        @endforeach
                    </datalist>
                    @error('departamento')
                        <div class="form-help error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input type="tel" name="telefono" class="form-input @error('telefono') error @enderror" 
                           placeholder="Ej: +34 612 345 678"
                           value="{{ old('telefono', $user->telefono) }}">
                    @error('telefono')
                        <div class="form-help error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Información de Cuenta -->
                <div class="form-group form-full-width">
                    <h3 style="color: #2c3e50; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #3498db;">Información de Cuenta</h3>
                </div>

                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <div class="password-container">
                        <input type="password" name="password" class="form-input @error('password') error @enderror" 
                               placeholder="Dejar en blanco para mantener la actual"
                               minlength="8" id="password-input">
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="form-help">Mínimo 8 caracteres con mayúsculas, minúsculas y números. Solo complete si desea cambiar la contraseña.</div>
                    @error('password')
                        <div class="form-help error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Confirmar Contraseña</label>
                    <div class="password-container">
                        <input type="password" name="password_confirmation" class="form-input @error('password_confirmation') error @enderror" 
                               placeholder="Repita la nueva contraseña"
                               minlength="8" id="confirm-password-input">
                        <button type="button" class="password-toggle" id="toggleConfirmPassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <small id="password-match" style="font-size: 0.75rem; display: none;"></small>
                    @error('password_confirmation')
                        <div class="form-help error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Verificación de Seguridad -->
                <div class="form-group form-full-width">
                    <h3 style="color: #2c3e50; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #3498db;">Verificación de Seguridad</h3>
                </div>

                <div class="form-group form-full-width">
                    <label class="form-label">
                        Contraseña Actual <span class="required">*</span>
                    </label>
                    <div class="password-container">
                        <input type="password" name="current_password" class="form-input @error('current_password') error @enderror" 
                               placeholder="Ingrese su contraseña actual para confirmar los cambios" required
                               id="current-password-input">
                        <button type="button" class="password-toggle" id="toggleCurrentPassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="form-help">Ingresar contraseña para guardar los cambios.</div>
                    @error('current_password')
                        <div class="form-help error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    Actualizar Usuario
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Estilos iguales a create.blade.php */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f5f6fa;
        color: #333;
    }

    .container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .form-header {
        background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
        color: white;
        padding: 30px;
        text-align: center;
    }

    .form-header h1 {
        font-size: 28px;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .form-header p {
        opacity: 0.9;
        font-size: 16px;
    }

    .form-body {
        padding: 40px;
    }

    /* Mensajes */
    .message {
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        font-size: 14px;
        font-weight: 500;
    }

    .message.success {
        background: #d5f4e6;
        color: #27ae60;
        border: 1px solid #27ae60;
    }

    .message.error {
        background: #fdeaea;
        color: #e74c3c;
        border: 1px solid #e74c3c;
    }

    /* Layout del formulario */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-full-width {
        grid-column: 1 / -1;
    }

    /* Grupos de formulario */
    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #2c3e50;
        font-size: 14px;
    }

    .form-label .required {
        color: #e74c3c;
    }

    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e1e8ed;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: #f8f9fa;
        font-family: inherit;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline: none;
        border-color: #3498db;
        background: white;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    .form-input.error, .form-select.error {
        border-color: #e74c3c;
    }

    .form-help {
        font-size: 12px;
        color: #7f8c8d;
        margin-top: 5px;
    }

    .form-help.error {
        color: #e74c3c;
    }

    /* Password container - ESTILOS MEJORADOS */
    .password-container {
        position: relative;
    }

    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #7f8c8d;
        cursor: pointer;
        padding: 6px;
        border-radius: 4px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        z-index: 2;
    }

    .password-toggle:hover {
        background-color: #e1e8ed;
        color: #3498db;
    }

    .password-toggle:focus {
        outline: none;
        box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.3);
    }

    .password-toggle.active {
        color: #3498db;
        background-color: #e3f2fd;
    }

    /* Ajustar padding para inputs de contraseña */
    .password-container .form-input {
        padding-right: 50px;
    }

    /* Botones */
    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e1e8ed;
    }

    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(52, 152, 219, 0.3);
    }

    .btn-secondary {
        background: #f8f9fa;
        color: #2c3e50;
    }

    .btn-secondary:hover {
        background: #3498db;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(52, 152, 219, 0.3);
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

    /* Responsive */
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
        
        .form-body {
            padding: 30px 20px;
        }
        
        .form-header {
            padding: 25px 20px;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<!-- Agregar Font Awesome para los íconos -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('scripts')
<script>
    // Toggle password visibility - FUNCIÓN MEJORADA
    function togglePassword(button, inputId) {
        const input = document.getElementById(inputId);
        const icon = button.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
            button.classList.add('active');
            button.setAttribute('aria-label', 'Ocultar contraseña');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
            button.classList.remove('active');
            button.setAttribute('aria-label', 'Mostrar contraseña');
        }
        
        // Mantener el foco en el input
        input.focus();
    }

    // Inicializar toggles de contraseña
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar toggles para todas las contraseñas
        const toggles = [
            { btnId: 'togglePassword', inputId: 'password-input' },
            { btnId: 'toggleConfirmPassword', inputId: 'confirm-password-input' },
            { btnId: 'toggleCurrentPassword', inputId: 'current-password-input' }
        ];

        toggles.forEach(toggle => {
            const btn = document.getElementById(toggle.btnId);
            if (btn) {
                btn.addEventListener('click', function() {
                    togglePassword(this, toggle.inputId);
                });
                
                // Accesibilidad con teclado
                btn.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        togglePassword(this, toggle.inputId);
                    }
                });
            }
        });

        // Verificar coincidencia de contraseñas
        function checkPasswordMatch() {
            const password = document.getElementById('password-input').value;
            const confirm = document.getElementById('confirm-password-input').value;
            const matchElement = document.getElementById('password-match');
            
            if (confirm.length === 0) {
                matchElement.style.display = 'none';
                return;
            }
            
            if (password === confirm) {
                matchElement.textContent = '✓ Las contraseñas coinciden';
                matchElement.style.color = '#27ae60';
            } else {
                matchElement.textContent = '✗ Las contraseñas no coinciden';
                matchElement.style.color = '#e74c3c';
            }
            matchElement.style.display = 'block';
        }

        // Event listeners para verificar coincidencia
        document.getElementById('password-input').addEventListener('input', checkPasswordMatch);
        document.getElementById('confirm-password-input').addEventListener('input', checkPasswordMatch);

        // Funciones de validación
        function mostrarError(campo, mensaje) {
            removerError(campo);
            
            campo.classList.add('error');
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'form-help error';
            errorDiv.id = `error-${campo.id || campo.name}`;
            errorDiv.textContent = mensaje;
            errorDiv.style.display = 'block';
            
            campo.parentNode.appendChild(errorDiv);
        }

        function removerError(campo) {
            campo.classList.remove('error');
            const errorId = `error-${campo.id || campo.name}`;
            const errorExistente = document.getElementById(errorId);
            if (errorExistente) {
                errorExistente.remove();
            }
        }

        // Validación específica para contraseñas
        function validarPassword(password, confirmPassword) {
            let valido = true;
            
            // Limpiar errores previos
            removerError(password);
            removerError(confirmPassword);

            // Solo validar si hay valor en la contraseña
            if (password && password.value) {
                if (password.value.length < 8) {
                    mostrarError(password, 'La contraseña debe tener al menos 8 caracteres');
                    valido = false;
                }
                
                if (confirmPassword && password.value !== confirmPassword.value) {
                    mostrarError(confirmPassword, 'Las contraseñas no coinciden');
                    valido = false;
                }
            }
            
            return valido;
        }

        // Validación del formulario completo
        function validarFormularioUsuario(event) {
            const formulario = event.target;
            let valido = true;

            // Limpiar todos los errores previos
            formulario.querySelectorAll('.form-help.error').forEach(error => error.remove());
            formulario.querySelectorAll('.error').forEach(campo => campo.classList.remove('error'));

            // Validar campos requeridos
            const camposRequeridos = ['nombre', 'username', 'email', 'rol', 'current_password'];
            camposRequeridos.forEach(campoName => {
                const campo = formulario.querySelector(`[name="${campoName}"]`);
                if (campo && !campo.value.trim()) {
                    mostrarError(campo, 'Este campo es obligatorio');
                    valido = false;
                }
            });

            // Validar formato de email
            const email = formulario.querySelector('[name="email"]');
            if (email && email.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
                mostrarError(email, 'El formato del correo electrónico no es válido');
                valido = false;
            }

            // Validar contraseñas (solo si se están cambiando)
            const password = formulario.querySelector('[name="password"]');
            const confirmPassword = formulario.querySelector('[name="password_confirmation"]');
            
            if (password && password.value) {
                if (!validarPassword(password, confirmPassword)) {
                    valido = false;
                }
            }

            // Si no es válido, prevenir envío y mostrar scroll al primer error
            if (!valido) {
                event.preventDefault();
                const primerError = formulario.querySelector('.error');
                if (primerError) {
                    primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }

            return valido;
        }

        // Prevenir envío del formulario si no es válido
        const formularioUsuario = document.getElementById('form-editar-usuario');
        if (formularioUsuario) {
            formularioUsuario.addEventListener('submit', validarFormularioUsuario);
        }
    });
</script>
@endsection