<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FactuWeb PRO - Software de Facturación y POS</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- FontAwesome 6.5.0 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/favicon.svg') }}">

    <!-- Vite Styles (app.css) & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    <!-- Top Banner -->
    <div class="top-banner">
        🚀 YA DISPONIBLE: FactuWeb PRO V1.0 
        @if (Route::has('login'))
            @auth
                <a href="{{ url('/dashboard') }}">Ir al Panel &rarr;</a>
            @else
                <a href="{{ route('login') }}">Ingresar al Sistema &rarr;</a>
            @endauth
        @endif
    </div>

    <!-- 1. Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top shadow-lg z-3">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <!-- Nuevo Logo SVG -->
                <img src="{{ asset('img/logo_nuevo.svg') }}" alt="FactuWeb PRO Logo" class="logo-img">
            </a>
            
            <!-- Botones móviles visibles siempre -->
            <div class="d-flex align-items-center gap-2 d-lg-none">
                @if (Route::has('login'))
                    @auth
                        <a class="btn btn-white btn-sm px-3 fw-bold" href="{{ url('/dashboard') }}" style="padding: 0.4rem 1rem;">Panel</a>
                    @else
                        <a class="btn btn-white btn-sm px-3 fw-bold" href="{{ route('login') }}" style="padding: 0.4rem 1rem;">Ingresar</a>
                        @if (Route::has('register'))
                            <a class="btn btn-blue btn-sm px-3 fw-bold" href="{{ route('register') }}" style="padding: 0.4rem 1rem;">Registro</a>
                        @endif
                    @endauth
                @endif
                <button class="navbar-toggler border-0 shadow-none p-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <i class="fa-solid fa-bars text-white fs-2"></i>
                </button>
            </div>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link active" href="#inicio">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#caracteristicas">Características</a></li>
                    <li class="nav-item"><a class="nav-link" href="#funcionamiento">Cómo Funciona</a></li>
                    <li class="nav-item"><a class="nav-link" href="#beneficios">Beneficios</a></li>
                </ul>
                <!-- Botones escritorio -->
                <div class="d-flex align-items-center mt-3 mt-lg-0 gap-3 d-none d-lg-flex">
                    @if (Route::has('login'))
                        @auth
                            <a class="btn btn-white px-4" href="{{ url('/dashboard') }}">Ir al Panel</a>
                        @else
                            <a class="btn btn-white px-4" href="{{ route('login') }}">Ingresar</a>
                            @if (Route::has('register'))
                                <a class="btn btn-blue px-4" href="{{ route('register') }}">Registrarse</a>
                            @endif
                        @endauth
                    @endif
                    <div class="social-icons">
                        <a href="#"><i class="fa-brands fa-facebook"></i></a>
                        <a href="#"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- 2. Hero principal -->
    <div class="hero-wrapper" id="inicio">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0 pe-lg-5">
                    <h1 class="hero-title">
                        SOFTWARE DE FACTURACIÓN ELECTRÓNICA Y POS
                    </h1>
                    <p class="hero-text">
                        Somos el sistema más rápido, confiable y con el mejor soporte para pequeños negocios. Centraliza ventas, inventario y facturación DIAN en un solo lugar.
                    </p>
                    <div class="hero-cta-wrapper mt-4">
                        <div class="d-flex flex-column flex-sm-row gap-3">
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="btn btn-super-cta">
                                        Ir al Panel <i class="fa-solid fa-circle-arrow-right ms-2"></i>
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-super-cta">
                                        Comenzar Ahora <i class="fa-solid fa-circle-arrow-right ms-2"></i>
                                    </a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="btn btn-outline-hero">
                                            Registrarse
                                        </a>
                                    @endif
                                @endauth
                            @endif
                        </div>
                        <div class="mt-3 text-white-50 small d-flex align-items-center gap-3">
                            <span><i class="fa-solid fa-circle-check text-success me-1"></i> Configuración en 5 min</span>
                            <span><i class="fa-solid fa-circle-check text-success me-1"></i> Soporte VIP</span>
                        </div>
                    </div>
                    
                    <div class="trust-badge shadow">
                        4.9 <i class="fa-solid fa-star"></i> en <strong>&nbsp;Trustpilot</strong>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <!-- Dashboard Mockup con efecto 3D Isométrico -->
                    <div class="hero-mockup-wrapper">
                        <div class="d-flex justify-content-between align-items-center border-bottom border-secondary pb-3 mb-4">
                            <h5 class="m-0 fw-bold text-white">
                                <i class="fa-solid fa-border-all me-2 text-primary"></i>Panel de Control
                            </h5>
                            <div class="d-flex gap-2">
                                <span class="badge bg-success rounded-pill px-3 py-2">En Línea</span>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="stat-label">Ventas de Hoy</div>
                                    <h4 class="stat-value">$1.250.000</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="stat-label">Facturas DIAN</div>
                                    <h4 class="stat-value text-success">24 Emitidas</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card mb-0">
                                    <div class="stat-label">Clientes</div>
                                    <h4 class="stat-value">18 Nuevos</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card mb-0 border-warning">
                                    <div class="stat-label text-warning">Alertas Stock</div>
                                    <h4 class="stat-value text-warning">3 Productos</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Sección de características -->
    <section id="caracteristicas" class="section-padding bg-light-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">TODO LO NECESARIO PARA TU<br>NEGOCIO</h2>
                <p class="section-subtitle">Compatible con tiendas, minimarkets, ferreterías, servicios y más.</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <!-- Icono SVG ilustrativo -->
                        <div class="feature-icon text-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <h5 class="feature-title">Facturación Electrónica</h5>
                        <p class="feature-text">Genera facturas electrónicas de venta cumpliendo con todos los requisitos de la DIAN al instante y sin demoras.</p>
                        <a href="{{ route('login') }}" class="btn btn-blue btn-sm px-4">Ver Detalles &rarr;</a>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon text-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <h5 class="feature-title">Control de Inventario</h5>
                        <p class="feature-text">Gestiona tu catálogo de productos, valida el stock en tiempo real durante la venta y recibe alertas de agotamiento.</p>
                        <a href="{{ route('login') }}" class="btn btn-blue btn-sm px-4">Ver Detalles &rarr;</a>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon text-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <h5 class="feature-title">Gestión de Clientes</h5>
                        <p class="feature-text">Automatiza la recepción y guarda el historial de compras de tus clientes para ofrecer un mejor servicio.</p>
                        <a href="{{ route('login') }}" class="btn btn-blue btn-sm px-4">Ver Detalles &rarr;</a>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon text-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <h5 class="feature-title">Reportes y Finanzas</h5>
                        <p class="feature-text">Visualiza las utilidades, comisiones y el rendimiento general de tu empresa con gráficos interactivos.</p>
                        <a href="{{ route('login') }}" class="btn btn-blue btn-sm px-4">Ver Detalles &rarr;</a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon text-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <h5 class="feature-title">Seguridad y Roles</h5>
                        <p class="feature-text">Crea usuarios para tus cajeros y vendedores, limitando sus permisos solo a lo estrictamente necesario.</p>
                        <a href="{{ route('login') }}" class="btn btn-blue btn-sm px-4">Ver Detalles &rarr;</a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon text-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <h5 class="feature-title">Rapidez en POS</h5>
                        <p class="feature-text">Interfaz de punto de venta súper rápida. Diseñada para que no haya filas en tu mostrador.</p>
                        <a href="{{ route('login') }}" class="btn btn-blue btn-sm px-4">Ver Detalles &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Botón Flotante WhatsApp -->
    <a href="https://wa.me/" class="whatsapp-float" target="_blank" aria-label="Contacto por WhatsApp">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

    <!-- Footer -->
    <footer>
        <div class="container text-center text-md-start">
            <div class="row align-items-center">
                <div class="col-md-4 mb-4 mb-md-0">
                    <img src="{{ asset('img/logo_nuevo.svg') }}" alt="FactuWeb PRO" height="35" class="mb-3">
                    <p class="small mb-0 opacity-75">El sistema de facturación y POS líder para pequeños negocios en crecimiento.</p>
                </div>
                <div class="col-md-4 mb-4 mb-md-0 text-center">
                    <a href="#" class="text-white text-decoration-none mx-2 hover-opacity-100">Políticas de Privacidad</a>
                    <a href="#" class="text-white text-decoration-none mx-2 hover-opacity-100">Términos de Servicio</a>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="social-icons mb-2">
                        <a href="#" class="text-white text-decoration-none me-3 fs-5" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a>
                        <a href="#" class="text-white text-decoration-none me-3 fs-5" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="text-white text-decoration-none fs-5" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                    <span class="opacity-50 small">&copy; {{ date('Y') }} FactuWeb PRO. Todos los derechos reservados.</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
