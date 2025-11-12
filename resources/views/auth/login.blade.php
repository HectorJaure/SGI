<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - ITSN</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
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
            max-width: 400px;
            padding: 40px 30px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo h1 {
            color: var(--azul-marino);
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .logo p {
            color: var(--gris-medio);
            font-size: 0.9rem;
        }
        
        .login-form {
            width: 100%;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--gris-oscuro);
            font-size: 0.9rem;
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gris-medio);
        }
        
        input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1px solid var(--gris-claro);
            border-radius: 6px;
            font-size: 1rem;
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
            padding: 12px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            transition: background-color 0.3s;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn:hover {
            background-color: var(--azul-medio);
        }
        
        .alert {
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 0.9rem;
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

        .forgot-password {
            text-align: center;
            margin: 15px 0;
        }

        .forgot-password a {
            color: var(--azul-claro);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }

        .forgot-password a:hover {
            color: var(--azul-medio);
            text-decoration: underline;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--gris-claro);
        }
        
        .footer p {
            color: var(--gris-medio);
            font-size: 0.8rem;
        }
        
        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>Sistemas de Gestión de Seguridad Ocupacional</h1>
            <p>ITSN - ISO 45001:2018</p>
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
        
        <form class="login-form" method="POST" action="{{ route('login.submit') }}">
            @csrf
            <div class="form-group">
                <label for="username">Usuario</label>
                <div class="input-with-icon">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" id="username" name="username" placeholder="Ingrese su usuario" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <div class="input-with-icon">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required>
                </div>
            </div>

            <!-- LINK PARA RECUPERAR CONTRASEÑA -->
            <div class="forgot-password">
                <a href="{{ route('password.request') }}">
                    <i class="fas fa-key"></i> ¿Olvidaste tu contraseña?
                </a>
            </div>
            
            <button type="submit" class="btn">
                <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
            </button>
        </form>
        
        <div class="footer">
            <p>Instituto Tecnológico Superior de Nochistlán<br></p>
        </div>
    </div>
</body>
</html>