<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - ITSN</title>
    <style>
        :root {
            --azul-marino: #1e3a5f;
            --azul-medio: #2c5282;
            --azul-claro: #4299e1;
            --gris-oscuro: #4a5568;
            --gris-medio: #718096;
            --gris-claro: #e2e8f0;
            --blanco: #ffffff;
            --verde: #38a169;
            --rojo: #e53e3e;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, var(--azul-marino) 0%, var(--azul-medio) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: var(--blanco);
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            padding: 30px 25px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .logo h1 {
            color: var(--azul-marino);
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .logo p {
            color: var(--gris-medio);
            font-size: 0.85rem;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: var(--gris-oscuro);
            font-size: 0.85rem;
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gris-medio);
            font-size: 0.9rem;
        }
        
        input {
            width: 100%;
            padding: 10px 12px 10px 40px;
            border: 1px solid var(--gris-claro);
            border-radius: 6px;
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        
        input:focus {
            outline: none;
            border-color: var(--azul-claro);
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.2);
        }
        
        .btn {
            width: 100%;
            background-color: var(--azul-claro);
            color: white;
            border: none;
            padding: 10px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            transition: background-color 0.3s;
            margin-top: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        
        .btn:hover {
            background-color: var(--azul-medio);
        }
        
        .alert {
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 0.85rem;
        }
        
        .alert-error {
            background-color: #fed7d7;
            color: var(--rojo);
            border: 1px solid #feb2b2;
        }
        
        .alert-success {
            background-color: #c6f6d5;
            color: var(--verde);
            border: 1px solid #9ae6b4;
        }

        .back-to-login {
            text-align: center;
            margin: 12px 0;
        }

        .back-to-login a {
            color: var(--azul-claro);
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.3s;
        }

        .back-to-login a:hover {
            color: var(--azul-medio);
            text-decoration: underline;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid var(--gris-claro);
        }
        
        .footer p {
            color: var(--gris-medio);
            font-size: 0.75rem;
        }

        .instructions {
            background-color: #f7fafc;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 0.8rem;
            color: var(--gris-oscuro);
        }

        .user-info {
            background-color: #e3f2fd;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
            border-left: 4px solid var(--azul-claro);
            font-size: 0.85rem;
        }
        
        .user-info p {
            margin-bottom: 5px;
        }
        
        .user-info p:last-child {
            margin-bottom: 0;
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 25px 20px;
                max-width: 95%;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>Restablecer Contraseña</h1>
            <p>Sistema de Seguridad Ocupacional</p>
        </div>
        
        @if(session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <div class="user-info">
            <p><strong>Usuario:</strong> {{ $userName }}</p>
            <p><strong>Correo electrónico:</strong> {{ $userEmail }}</p>
        </div>

        <div class="instructions">
            <p><strong>Instrucciones:</strong> Ingresa tu nueva contraseña. Debe tener al menos 6 caracteres.</p>
        </div>
        
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $userEmail }}">
            
            <div class="form-group">
                <label for="password">Nueva Contraseña</label>
                <div class="input-with-icon">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="password" name="password" placeholder="Nueva contraseña" required minlength="6">
                </div>
                @error('password')
                    <small style="color: var(--rojo); font-size: 0.75rem;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmar Nueva Contraseña</label>
                <div class="input-with-icon">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirmar nueva contraseña" required minlength="6">
                </div>
            </div>
            
            <button type="submit" class="btn">
                <i class="fas fa-save"></i> Restablecer Contraseña
            </button>

            <div class="back-to-login">
                <a href="{{ route('login') }}">
                    <i class="fas fa-arrow-left"></i> Volver al Inicio de Sesión
                </a>
            </div>
        </form>
        
        <div class="footer">
            <p>Instituto Tecnológico Superior de Nochistlán</p>
        </div>
    </div>
</body>
</html>