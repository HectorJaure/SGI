<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión Integrado - ITSN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
            --amarillo: #d69e2e;
            --naranja: #dd6b20;
            --rojo: #e53e3e;
            --purpura: #805ad5;
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--azul-marino) 0%, var(--azul-medio) 100%);
            color: white;
            padding: 30px 0;
            margin-bottom: 20px;
        }
        .norma-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        .disabled-card {
            opacity: 0.6;
            cursor: not-allowed;
            border-left: 4px solid var(--gris-medio) !important;
        }
        .disabled-card:hover {
            transform: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card-title {
            color: var(--azul-marino);
        }
        .card-text {
            color: var(--gris-oscuro);
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="display-4 fw-bold mb-4">Sistema de Gestión Integrado</h1>
                    <p class="lead fs-5">Instituto Tecnológico Superior de Nochistlán</p>
                    <p class="mb-0">Selecciona la norma del sistema de gestión que deseas consultar</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards Section -->
    <div class="container">
        <div class="row g-4">
            <!-- Card 1: ISO 14001 - Deshabilitada por ahora -->
            <div class="col-md-4">
                <div class="card card-hover h-100 text-center p-4 disabled-card">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-leaf norma-icon text-success"></i>
                        </div>
                        <h4 class="card-title fw-bold">ISO 14001:2018</h4>
                        <h6 class="text-muted mb-3">Sistema de Gestión Ambiental</h6>
                        <p class="card-text">
                            Norma internacional que especifica los requisitos para un sistema de gestión ambiental efectivo. 
                            Enfocada en la protección del medio ambiente y la respuesta al cambio climático.
                        </p>
                        <a href="{{ route('login') }}" class="btn btn-secondary btn-custom mt-3" disabled> 
                            <i class="fas fa-lock me-2"></i>Próximamente
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card 2: ISO 45001:2018 - ACTIVA (redirige al login) -->
            <div class="col-md-4">
                <div class="card card-hover h-100 text-center p-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-hard-hat norma-icon text-warning"></i>
                        </div>
                        <h4 class="card-title fw-bold">ISO 45001:2018</h4>
                        <h6 class="text-muted mb-3">Seguridad y Salud en el Trabajo</h6>
                        <p class="card-text">
                            Norma que proporciona un marco para mejorar la seguridad de los empleados, 
                            reducir los riesgos en el lugar de trabajo y crear mejores condiciones laborales.
                        </p>
                        <a href="{{ route('login') }}" class="btn btn-warning btn-custom text-white mt-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Acceder al Sistema
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card 3: ISO 50001:2018 - Deshabilitada por ahora -->
            <div class="col-md-4">
                <div class="card card-hover h-100 text-center p-4 disabled-card">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-bolt norma-icon text-info"></i>
                        </div>
                        <h4 class="card-title fw-bold">ISO 50001:2018</h4>
                        <h6 class="text-muted mb-3">Sistema de Gestión de la Energía</h6>
                        <p class="card-text">
                            Norma que ayuda a las organizaciones a mejorar su desempeño energético, 
                            aumentar la eficiencia energética y reducir los costos asociados al consumo de energía.
                        </p>
                        <a href="{{ route('login') }}" class="btn btn-secondary btn-custom mt-3" disabled>
                            <i class="fas fa-lock me-2"></i>Próximamente
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>