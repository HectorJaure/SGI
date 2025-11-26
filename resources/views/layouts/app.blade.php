<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Sistema SGSST - ITSN')</title>
<link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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

.header-mobile {
    display: none;
    background-color: var(--azul-marino);
    color: var(--blanco);
    padding: 15px 20px;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.header-mobile-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.hamburger-btn {
    background: none;
    border: none;
    color: var(--blanco);
    font-size: 24px;
    cursor: pointer;
    padding: 5px;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.hamburger-btn:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.header-mobile-title {
    font-size: 18px;
    font-weight: 600;
}

.sidebar {
    background-color: var(--azul-marino);
    color: var(--blanco);
    min-height: 100vh;
    padding: 20px 0;
    position: fixed;
    width: 250px;
    transition: transform 0.3s ease;
    z-index: 999;
}

.sidebar-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 998;
}

.menu-item {
    padding: 12px 20px;
    display: flex;
    align-items: center;
    text-decoration: none;
    color: var(--blanco);
    transition: background-color 0.2s;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
}

.menu-item:hover, .menu-item.active {
    background-color: var(--azul-medio);
    color: var(--blanco);
}

.menu-item.active {
    border-left: 4px solid var(--azul-claro);
}

.main-content {
    background-color: #f7fafc;
    min-height: 100vh;
    margin-left: 250px;
    transition: margin-left 0.3s ease;
}

.content-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    border: none;
}

.page-title {
    color: var(--azul-marino);
    font-size: 1.8rem;
    font-weight: 600;
    margin-bottom: 20px;
    text-align: center;
}

.stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-left: 4px solid #3498db;
}

.stat-card.users { border-left-color: #3498db; }
.stat-card.active { border-left-color: #27ae60; }
.stat-card.inactive { border-left-color: #e74c3c; }
.stat-card.roles { border-left-color: #f39c12; }

.stat-card h3 {
    font-size: 14px;
    color: #7f8c8d;
    margin-bottom: 10px;
    text-transform: uppercase;
    font-weight: 500;
}

.stat-card .number {
    font-size: 32px;
    font-weight: 700;
    color: #2c3e50;
}

.status-banner {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 30px;
    border-left: 6px solid #27ae60;
}

.status-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.status-info h3 {
    color: #2c3e50;
    font-size: 20px;
    margin-bottom: 5px;
}

.status-info p {
    color: #7f8c8d;
}

.status-indicator {
    text-align: right;
}

.status-level {
    font-size: 32px;
    font-weight: 700;
    color: #27ae60;
}

.status-dates {
    font-size: 14px;
    color: #7f8c8d;
    margin-top: 5px;
}

.risk-chart-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    align-items: center;
}

.chart-wrapper {
    position: relative;
    height: 220px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chart-svg {
    width: 200px;
    height: 200px;
    transform: rotate(-90deg);
    filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));
}

.chart-background {
    fill: none;
    stroke: #f8f9fa;
    stroke-width: 10;
}

.chart-segment {
    fill: none;
    stroke-width: 10;
    stroke-linecap: round;
    transition: all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
    animation: chartAnimation 1.5s ease-out forwards;
}

@keyframes chartAnimation {
    from { stroke-dasharray: 0 100; }
}

.segment-alto { stroke: #e74c3c; }
.segment-medio { stroke: #f39c12; }
.segment-bajo { stroke: #27ae60; }

.chart-center {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    background: white;
    border-radius: 50%;
    width: 80px;
    height: 80px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.chart-total {
    font-size: 24px;
    font-weight: 800;
    color: #2c3e50;
    line-height: 1;
    margin-bottom: 2px;
}

.chart-label {
    font-size: 10px;
    color: #7f8c8d;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.chart-legend {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 8px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.legend-item:hover {
    transform: translateX(5px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.legend-color {
    width: 20px;
    height: 20px;
    border-radius: 6px;
    flex-shrink: 0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.legend-color.alto { background: #e74c3c; }
.legend-color.medio { background: #f39c12; }
.legend-color.bajo { background: #27ae60; }

.legend-info {
    flex: 1;
}

.legend-value {
    font-weight: 700;
    color: #2c3e50;
    font-size: 18px;
    line-height: 1;
    margin-bottom: 2px;
}

.legend-label {
    font-size: 11px;
    color: #7f8c8d;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.legend-percentage {
    font-weight: 800;
    color: #2c3e50;
    font-size: 16px;
    background: white;
    padding: 4px 8px;
    border-radius: 6px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.alert-item {
    display: flex;
    align-items: flex-start;
    padding: 15px 0;
    border-bottom: 1px solid #ecf0f1;
    transition: background-color 0.3s ease;
}

.alert-item:hover { background: #f8f9fa; }
.alert-item:last-child { border-bottom: none; }

.alert-icon {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    flex-shrink: 0;
}

.alert-icon.riesgo { background: #fdeaea; color: #e74c3c; }
.alert-icon.requisito { background: #e3f2fd; color: #3498db; }
.alert-icon.documentacion { background: #fff3e0; color: #f39c12; }

.alert-content { flex: 1; }

.alert-title {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 4px;
}

.alert-desc {
    font-size: 13px;
    color: #7f8c8d;
    margin-bottom: 4px;
}

.alert-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.alert-date {
    font-size: 12px;
    color: #7f8c8d;
}

.priority-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}

.priority-alta { background: #fdeaea; color: #e74c3c; }
.priority-media { background: #fff3e0; color: #f39c12; }

.quick-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 25px;
    background: #f8f9fa;
    border: 2px dashed #ddd;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    color: #333;
}

.action-btn:hover {
    border-color: #3498db;
    background: #e3f2fd;
    transform: translateY(-2px);
}

.action-icon {
    font-size: 28px;
    margin-bottom: 10px;
}

.action-text {
    font-size: 14px;
    font-weight: 500;
    text-align: center;
}

.pagination-info {
    color: var(--gris-medio);
    font-size: 0.9rem;
}

.pagination {
    display: flex;
    gap: 5px;
}

.pagination .page-link {
    padding: 8px 12px;
    border: 1px solid #ddd;
    background: white;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    color: #333;
}

.pagination .page-item.active .page-link {
    background: var(--azul-claro);
    color: white;
    border-color: var(--azul-claro);
}

@media (max-width: 768px) {
    .header-mobile {
        display: block;
        z-index: 1002;
    }

    .sidebar {
        transform: translateY(-100%);
        width: 100%;
        height: auto;
        max-height: none;
        overflow-y: visible;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: fixed;
        top: 70px;
        left: 0;
        right: 0;
        z-index: 1001;
        padding: 0;
        min-height: auto;
    }

    .sidebar.open { transform: translateY(0); }

    .sidebar-overlay.active {
        display: block;
        animation: fadeIn 0.3s ease;
        z-index: 1000;
        background-color: transparent;
    }

    .main-content {
        margin-left: 0;
        padding-top: 70px;
    }
    
    .sidebar .logo { display: none; }
    
    .sidebar .nav {
        padding: 10px 15px;
        background: var(--azul-marino);
        border-radius: 0 0 10px 10px;
        margin: 0;
    }
    
    .sidebar .nav-item { margin: 0; }
    
    .sidebar .nav-item:not(:last-child) { margin-bottom: 5px; }
    
    .menu-item {
        padding: 12px 15px;
        border-radius: 6px;
        margin: 2px 0;
    }
    
    .menu-item.active {
        border-left: 4px solid var(--azul-claro);
        background-color: var(--azul-medio);
    }
    
    body.sidebar-open { overflow: hidden; }
    
    .risk-chart-container { grid-template-columns: 1fr; }
    .quick-actions { grid-template-columns: 1fr; }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.sidebar {
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: var(--azul-medio) var(--azul-marino);
}

.menu-item { display: flex; align-items: center; }

.menu-item i { width: 20px; margin-right: 10px; text-align: center; }

.sidebar::-webkit-scrollbar { width: 6px; }

.sidebar::-webkit-scrollbar-track { background: var(--azul-marino); }

.sidebar::-webkit-scrollbar-thumb {
    background-color: var(--azul-medio);
    border-radius: 3px;
}

.hamburger-btn {
    transition: all 0.3s ease;
    z-index: 1003;
}

.hamburger-btn:active { transform: scale(0.95); }

.header-controls {
    display: flex;
    align-items: center;
    gap: 15px;
}

.pagination-selector { display: flex; align-items: center; }

.pagination-selector select {
    width: auto;
    min-width: 70px;
    padding: 6px 10px;
    border: 1px solid var(--gris-claro);
    border-radius: 6px;
    font-size: 0.9rem;
    background-color: var(--blanco);
    cursor: pointer;
}

.pagination-selector select:focus {
    outline: none;
    border-color: var(--azul-claro);
    box-shadow: 0 0 0 2px rgba(66, 153, 225, 0.1);
}

.form-group input[list]::-webkit-calendar-picker-indicator,
.datalist-input::-webkit-calendar-picker-indicator { display: none !important; }

.form-group input[list]::-webkit-list-button,
.datalist-input::-webkit-list-button { display: none !important; }

.form-group input[list]::-webkit-clear-button,
.datalist-input::-webkit-clear-button { display: none !important; }

.datalist-input {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}

.datalist-input { background-image: none !important; }
</style>
@yield('styles')
</head>
<body>
    @if(session('logged_in'))
    <div class="header-mobile">
        <div class="header-mobile-content">
            <button class="hamburger-btn" id="hamburgerBtn">
                <i class="fas fa-bars"></i>
            </button>
            <div class="header-mobile-title">Sistema SGSST</div>
            <div></div>
        </div>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 sidebar" id="sidebar">
                @include('partials.sidebar')
            </div>

            <div class="col-md-9 col-lg-10 main-content" id="mainContent">
                @include('partials.header')
                
                <main class="p-4">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>
    @else
        @yield('content')
    @endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const body = document.body;
    
    function toggleSidebar() {
        const isOpening = !sidebar.classList.contains('open');
        
        sidebar.classList.toggle('open');
        sidebarOverlay.classList.toggle('active');
        body.classList.toggle('sidebar-open', isOpening);
        
        const icon = hamburgerBtn.querySelector('i');
        if (isOpening) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
            body.style.overflow = 'hidden';
        } else {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
            body.style.overflow = '';
        }
    }
    
    if (hamburgerBtn) {
        hamburgerBtn.addEventListener('click', toggleSidebar);
    }
    
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', toggleSidebar);
    }
    
    const menuItems = document.querySelectorAll('.menu-item');
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                toggleSidebar();
            }
        });
    });
    
    function handleResize() {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('open');
            sidebarOverlay.classList.remove('active');
            body.classList.remove('sidebar-open');
            body.style.overflow = '';
            
            const icon = hamburgerBtn.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        }
    }
    
    window.addEventListener('resize', handleResize);
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('open')) {
            toggleSidebar();
        }
    });
});

function changePerPage(perPage) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', perPage);
    url.searchParams.set('page', '1');
    
    window.location.href = url.toString();
}

document.addEventListener('DOMContentLoaded', function() {
    const filterForms = document.querySelectorAll('form[method="GET"]');
    filterForms.forEach(form => {
        const perPage = new URLSearchParams(window.location.search).get('per_page');
        if (perPage) {
            let existingInput = form.querySelector('input[name="per_page"]');
            if (!existingInput) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'per_page';
                hiddenInput.value = perPage;
                form.appendChild(hiddenInput);
            }
        }
    });
});
</script>
@yield('scripts')
</body>
</html>