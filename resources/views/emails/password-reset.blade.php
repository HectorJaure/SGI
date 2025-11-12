<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Restablecer Contraseña - ITSN</title>
</head>
<body>
    <h2>Sistema SGSST - ITSN</h2>
    <p>Hola {{ $user->nombre ?? $user->username }},</p>
    
    <p>Has solicitado restablecer tu contraseña para el Sistema de Gestión de Seguridad y Salud en el Trabajo.</p>
    
    <p>Para continuar, haz clic en el siguiente enlace:</p>
    
    <p>
        <a href="{{ $resetUrl }}" style="background: #4299e1; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            🔐 Restablecer Contraseña
        </a>
    </p>
    
    <p>O copia esta URL en tu navegador:</p>
    <p><code>{{ $resetUrl }}</code></p>
    
    <p><strong>⏰ Este enlace expirará en 24 horas.</strong></p>
    
    <p>Si no solicitaste este restablecimiento, ignora este mensaje.</p>
    
    <hr>
    <p><em>Instituto Tecnológico Superior de Nochistlán<br>
    Sistema SGSST - ISO 45001:2018</em></p>
</body>
</html>