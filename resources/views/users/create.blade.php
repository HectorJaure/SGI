@extends('layouts.app')

@section('title', 'Crear Usuario - Sistema SGSST')

@section('header-title', 'Crear Usuario')

@section('content')
<div class="container">
    <div class="form-header">
        <h1>Crear Usuario</h1>
        <p>Complete la información del nuevo usuario</p>
    </div>

    <div class="form-body">

        <form method="POST" action="{{ route('users.store') }}" id="form-crear-usuario">
            @csrf
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
                           value="{{ old('nombre') }}">
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
                           value="{{ old('username') }}">
                    <div class="form-help">Se usara para iniciar sesion</div>
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
                           value="{{ old('email') }}">
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
                        <option value="Usuario" {{ old('rol') === 'Usuario' ? 'selected' : '' }}>Usuario</option>
                        <option value="Administrador" {{ old('rol') === 'Administrador' ? 'selected' : '' }}>Administrador</option>
                    </select>
                    @error('rol')
                        <div class="form-help error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Departamento</label>
                    <input list="departamentos-list" id="departamento" name="departamento" 
                        value="{{ old('departamento') }}" 
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
                           value="{{ old('telefono') }}">
                    @error('telefono')
                        <div class="form-help error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Información de Cuenta -->
                <div class="form-group form-full-width">
                    <h3 style="color: #2c3e50; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #3498db;">Información de Cuenta</h3>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Contraseña <span class="required">*</span>
                    </label>
                    <div class="password-container">
                        <input type="password" name="password" class="form-input @error('password') error @enderror" 
                               placeholder="Mínimo 6 caracteres" required
                               minlength="6" id="password-input">
                        <button type="button" class="toggle-password" onclick="togglePassword(this)">
                            👁️
                        </button>
                    </div>
                    <div class="password-strength">
                        <div class="strength-bar" id="strength-bar"></div>
                    </div>
                    <div class="strength-text" id="strength-text">Seguridad de la contraseña</div>
                    @error('password')
                        <div class="form-help error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Confirmar Contraseña <span class="required">*</span>
                    </label>
                    <div class="password-container">
                        <input type="password" name="password_confirmation" class="form-input @error('password_confirmation') error @enderror" 
                               placeholder="Repita la contraseña" required
                               minlength="6" id="confirm-password-input">
                        <button type="button" class="toggle-password" onclick="togglePassword(this)">
                            👁️
                        </button>
                    </div>
                    @error('password_confirmation')
                        <div class="form-help error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    Crear Usuario
                </button>
            </div>
        </form>
    </div>
</div>

<style>
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
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
        font-weight: 500;
        background-color: white;
    }

    /* Password container */
    .password-container {
        position: relative;
    }

    .toggle-password {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #7f8c8d;
        cursor: pointer;
        padding: 4px;
    }

    /* Password strength */
    .password-strength {
        margin-top: 8px;
        height: 4px;
        background: #e1e8ed;
        border-radius: 2px;
        overflow: hidden;
    }

    .strength-bar {
        height: 100%;
        width: 0%;
        transition: all 0.3s ease;
        border-radius: 2px;
    }

    .strength-weak { background: #e74c3c; width: 33%; }
    .strength-medium { background: #f39c12; width: 66%; }
    .strength-strong { background: #27ae60; width: 100%; }

    .strength-text {
        font-size: 12px;
        color: #7f8c8d;
        margin-top: 4px;
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
        background-color: #fed7d7;
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
@endsection

@section('scripts')
<script>
    // Toggle password visibility
    function togglePassword(button) {
        const input = button.parentElement.querySelector('input');
        if (input.type === 'password') {
            input.type = 'text';
            button.textContent = '🔒';
        } else {
            input.type = 'password';
            button.textContent = '👁️';
        }
    }

    function mostrarError(campo, mensaje) {
        removerError(campo);
        
        campo.classList.add('error');
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.id = `error-${campo.id || campo.name}`;
        errorDiv.textContent = mensaje;
        
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

    // Check password strength
    function checkPasswordStrength(password) {
        const strengthBar = document.getElementById('strength-bar');
        const strengthText = document.getElementById('strength-text');
        
        let strength = 0;
        let text = 'Muy débil';
        let className = 'strength-weak';

        if (password.length >= 6) strength++;
        if (password.length >= 8) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;

        switch (strength) {
            case 0:
            case 1:
                text = 'Débil';
                className = 'strength-weak';
                break;
            case 2:
            case 3:
                text = 'Media';
                className = 'strength-medium';
                break;
            case 4:
            case 5:
                text = 'Fuerte';
                className = 'strength-strong';
                break;
        }

        strengthBar.className = 'strength-bar ' + className;
        strengthText.textContent = text;
    }

    // Validación simple del campo nombre (solo formato)
    function validarNombre(campo) {
        const valor = campo.value.trim();
        
        // Limpiar error previo
        removerError(campo);
        
        // Si el campo está vacío, no mostrar error de formato
        if (!valor) {
            return true;
        }

        // Validar que solo contenga letras, espacios y acentos
        if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(valor)) {
            mostrarError(campo, 'El formato del campo nombre no es válido. Solo se permiten letras y espacios.');
            return false;
        }

        return true;
    }

    // Validación específica para contraseñas
    function validarPassword(password, confirmPassword) {
        let valido = true;
        
        // Limpiar errores previos
        removerError(password);
        removerError(confirmPassword);

        if (password && password.value) {
            // Validar longitud de contraseña principal
            if (password.value.length < 6) {
                mostrarError(password, 'La contraseña debe tener al menos 6 caracteres');
                valido = false;
            }
            
            // Validar coincidencia solo si confirmPassword tiene valor
            if (confirmPassword && confirmPassword.value) {
                if (password.value !== confirmPassword.value) {
                    mostrarError(confirmPassword, 'Las contraseñas no coinciden');
                    valido = false;
                }
            }
        }
        
        return valido;
    }

    // Guardar contraseñas temporalmente
    function guardarContraseñasTemporalmente(formulario) {
        const password = formulario.querySelector('[name="password"]');
        const confirmPassword = formulario.querySelector('[name="password_confirmation"]');
        
        if (password && password.value) {
            sessionStorage.setItem('tempPassword', password.value);
        }
        if (confirmPassword && confirmPassword.value) {
            sessionStorage.setItem('tempConfirmPassword', confirmPassword.value);
        }
    }

    // Restaurar contraseñas temporalmente
    function restaurarContraseñasTemporalmente(formulario) {
        const tempPassword = sessionStorage.getItem('tempPassword');
        const tempConfirmPassword = sessionStorage.getItem('tempConfirmPassword');
        
        const password = formulario.querySelector('[name="password"]');
        const confirmPassword = formulario.querySelector('[name="password_confirmation"]');
        
        if (tempPassword && password) {
            password.value = tempPassword;
        }
        if (tempConfirmPassword && confirmPassword) {
            confirmPassword.value = tempConfirmPassword;
        }
        
        // Limpiar almacenamiento temporal
        sessionStorage.removeItem('tempPassword');
        sessionStorage.removeItem('tempConfirmPassword');
    }

    // Validación del formulario completo al enviar
    function validarFormularioUsuario(event) {
        const formulario = event.target;
        let valido = true;

        // Guardar contraseñas antes de la validación
        guardarContraseñasTemporalmente(formulario);

        // Limpiar todos los errores previos
        formulario.querySelectorAll('.error-message').forEach(error => error.remove());
        formulario.querySelectorAll('.error').forEach(campo => campo.classList.remove('error'));

        // Validar campos requeridos
        const camposRequeridos = ['nombre', 'username', 'email', 'rol'];
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

        // Validar formato de nombre (solo si tiene valor)
        const nombre = formulario.querySelector('[name="nombre"]');
        if (nombre && nombre.value && !/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(nombre.value)) {
            mostrarError(nombre, 'El formato del campo nombre no es válido. Solo se permiten letras y espacios.');
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

        // Si no es válido, prevenir envío y restaurar contraseñas
        if (!valido) {
            event.preventDefault();
            
            // Pequeño delay para asegurar que las contraseñas se restauren después del preventDefault
            setTimeout(() => {
                restaurarContraseñasTemporalmente(formulario);
                
                // Hacer scroll al primer error
                const primerError = formulario.querySelector('.error');
                if (primerError) {
                    primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }, 100);
        } else {
            // Si es válido, limpiar el almacenamiento temporal
            sessionStorage.removeItem('tempPassword');
            sessionStorage.removeItem('tempConfirmPassword');
        }

        return valido;
    }

    // Inicialización cuando el DOM está listo
    document.addEventListener('DOMContentLoaded', function() {
        const formularioUsuario = document.getElementById('form-crear-usuario') || document.getElementById('form-editar-usuario');
        
        if (formularioUsuario) {
            // Password strength real-time check (solo para crear usuario)
            const passwordInput = document.getElementById('password-input');
            if (passwordInput) {
                passwordInput.addEventListener('input', function(e) {
                    checkPasswordStrength(e.target.value);
                });
            }

            // Restaurar contraseñas si existen en el almacenamiento temporal
            setTimeout(() => {
                restaurarContraseñasTemporalmente(formularioUsuario);
            }, 50);

            // Validación del campo NOMBRE solo al salir (blur)
            const campoNombre = formularioUsuario.querySelector('[name="nombre"]');
            if (campoNombre) {
                campoNombre.addEventListener('blur', function() {
                    validarNombre(this);
                });
            }

            // Validación del campo EMAIL solo al salir (blur)
            const campoEmail = formularioUsuario.querySelector('[name="email"]');
            if (campoEmail) {
                campoEmail.addEventListener('blur', function() {
                    const valor = this.value.trim();
                    removerError(this);
                    
                    if (valor && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valor)) {
                        mostrarError(this, 'El formato del correo electrónico no es válido');
                    }
                });
            }

            // Validación en tiempo real para contraseñas
            const password = formularioUsuario.querySelector('[name="password"]');
            const confirmPassword = formularioUsuario.querySelector('[name="password_confirmation"]');
            
            if (password) {
                password.addEventListener('input', function() {
                    validarPassword(password, confirmPassword);
                });
            }
            
            if (confirmPassword) {
                confirmPassword.addEventListener('input', function() {
                    validarPassword(password, confirmPassword);
                });
            }

            // Prevenir envío del formulario si no es válido
            formularioUsuario.addEventListener('submit', validarFormularioUsuario);
        }
    });
</script>
@endsection