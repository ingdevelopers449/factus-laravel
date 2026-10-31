<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="admin-html">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('tituloPagina', 'FactuWeb PRO - Panel de Administración')</title>

    <!-- Bootstrap 5.3 CSS -->
    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- FontAwesome 6.5.0 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/favicon.svg') }}">

    <!-- Vite Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('css')
</head>
<body class="admin-body">
    @php
        $user = Auth::user();
        $usuario_nombre = $user?->name ?? 'Usuario';
        $usuario_inicial = Str::upper(Str::substr($usuario_nombre, 0, 1));
        $role_code = strtolower($user?->role?->code ?? '');
        $is_admin = in_array($role_code, ['admin', 'administrador'], true);
        $nombre_rol = $user?->role?->name ?? ($is_admin ? 'Administrador' : 'Empleado');
        $color_rol = $is_admin ? '#ea580c' : '#10b981';

        // Mapa de breadcrumbs por nombre de ruta
        $current_route = Route::currentRouteName() ?? '';
        $breadcrumb_map = [
            'admin.dashboard' => [
                ['icono' => 'fa-chart-pie', 'label' => 'Dashboard']
            ],
            'dashboard' => [
                ['icono' => 'fa-chart-pie', 'label' => 'Dashboard']
            ],
            'facturas' => [
                ['icono' => 'fa-file-invoice-dollar', 'label' => 'Ventas'],
                ['icono' => 'fa-cash-register', 'label' => 'Facturación POS']
            ],
            'admin.facturas' => [
                ['icono' => 'fa-file-invoice-dollar', 'label' => 'Ventas'],
                ['icono' => 'fa-cash-register', 'label' => 'Facturación POS']
            ],
            'clientes' => [
                ['icono' => 'fa-file-invoice-dollar', 'label' => 'Ventas'],
                ['icono' => 'fa-users', 'label' => 'Clientes']
            ],
            'clientes.index' => [
                ['icono' => 'fa-file-invoice-dollar', 'label' => 'Ventas'],
                ['icono' => 'fa-users', 'label' => 'Clientes']
            ],
            'categorias' => [
                ['icono' => 'fa-box-open', 'label' => 'Catálogo'],
                ['icono' => 'fa-tags', 'label' => 'Categorías']
            ],
            'categorias.index' => [
                ['icono' => 'fa-box-open', 'label' => 'Catálogo'],
                ['icono' => 'fa-tags', 'label' => 'Categorías']
            ],
            'producto' => [
                ['icono' => 'fa-box-open', 'label' => 'Catálogo'],
                ['icono' => 'fa-box-open', 'label' => 'Productos']
            ],
            'producto.index' => [
                ['icono' => 'fa-box-open', 'label' => 'Catálogo'],
                ['icono' => 'fa-box-open', 'label' => 'Productos']
            ],
            'usuario' => [
                ['icono' => 'fa-user-shield', 'label' => 'Administración'],
                ['icono' => 'fa-user-shield', 'label' => 'Empleados']
            ],
            'usuario.index' => [
                ['icono' => 'fa-user-shield', 'label' => 'Administración'],
                ['icono' => 'fa-user-shield', 'label' => 'Empleados']
            ],
            'empresas' => [
                ['icono' => 'fa-building', 'label' => 'Administración'],
                ['icono' => 'fa-building', 'label' => 'Empresa']
            ],
            'empresas.index' => [
                ['icono' => 'fa-building', 'label' => 'Administración'],
                ['icono' => 'fa-building', 'label' => 'Empresa']
            ],
            'parametrizacion' => [
                ['icono' => 'fa-building-columns', 'label' => 'Administración'],
                ['icono' => 'fa-building-columns', 'label' => 'Parametrización DIAN']
            ],
            'admin.parametrizacion' => [
                ['icono' => 'fa-building-columns', 'label' => 'Administración'],
                ['icono' => 'fa-building-columns', 'label' => 'Parametrización DIAN']
            ],
            'rentabilidad' => [
                ['icono' => 'fa-chart-line', 'label' => 'Reportes'],
                ['icono' => 'fa-chart-line', 'label' => 'Ventas y Rentabilidad']
            ],
            'admin.rentabilidad' => [
                ['icono' => 'fa-chart-line', 'label' => 'Reportes'],
                ['icono' => 'fa-chart-line', 'label' => 'Ventas y Rentabilidad']
            ],
            'profile.edit' => [
                ['icono' => 'fa-user-gear', 'label' => 'Mi Perfil']
            ]
        ];
        $breadcrumbs = $breadcrumb_map[$current_route] ?? null;
        if (! $breadcrumbs) {
            foreach ($breadcrumb_map as $pattern => $crumbs) {
                if (request()->routeIs($pattern) || request()->routeIs($pattern . '.*')) {
                    $breadcrumbs = $crumbs;
                    break;
                }
            }
        }
        $breadcrumbs = $breadcrumbs ?? [['icono' => 'fa-chart-pie', 'label' => 'Dashboard']];
    @endphp

    <div class="app-container d-flex">

        <!-- Sidebar -->
        <aside class="app-sidebar text-white shadow-lg" id="sidebar">
            <div class="sidebar-brand d-flex align-items-center justify-content-center border-bottom border-light border-opacity-10" style="height: 80px; min-height: 80px;">
                <a href="{{ route('admin.dashboard') }}" class="text-decoration-none d-flex align-items-center justify-content-center w-100 px-2 overflow-hidden">
                    <img src="{{ asset('img/favicon.svg') }}" alt="Logo" class="sidebar-logo-mini" height="38">
                    <img src="{{ asset('img/logo_nuevo.svg') }}" alt="FactuWeb PRO" class="sidebar-logo-full" height="48">
                </a>
            </div>

            <ul class="nav flex-column sidebar-nav mt-3 overflow-auto pb-5">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-item sidebar-heading" title="Ventas">
                    <span class="heading-text">Ventas</span>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                        <span class="nav-text">Facturación POS</span>
                    </a>
                </li>
                <li class="nav-item">
                    @if (auth()->user()->role?->code === 'admin')
                        <a href="#" class="nav-link">
                            <i class="fa-solid fa-receipt"></i>
                            <span class="nav-text">Facturas</span>
                        </a>
                    @endif
                </li>
                <li class="nav-item">
                    @if (auth()->user()->role?->code === 'admin')
                        <a href="{{ route('clientes.index') }}" class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-users"></i>
                            <span class="nav-text">Clientes</span>
                        </a>
                    @endif
                </li>

                <li class="nav-item sidebar-heading" title="Catálogo">
                    <span class="heading-text">Catálogo</span>
                </li>
                <li class="nav-item">
                    @if (auth()->user()->role?->code === 'admin')
                        <a href="{{ route('categorias.index') }}" class="nav-link {{ request()->routeIs('categorias.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-tags"></i>
                            <span class="nav-text">Categorías</span>
                        </a>
                    @endif
                </li>
                <li class="nav-item">
                    @if (auth()->user()->role?->code === 'admin')
                        <a href="{{ route('producto.index') }}" class="nav-link {{ request()->routeIs('producto.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-box-open"></i>
                            <span class="nav-text">Productos</span>
                        </a>
                    @endif
                </li>

                <li class="nav-item sidebar-heading" title="Administración">
                    <span class="heading-text">Administración</span>
                </li>
                <li class="nav-item">
                    @if (auth()->user()->role?->code === 'admin')
                        <a href="{{ route('usuario.index') }}" class="nav-link {{ request()->routeIs('usuario.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-user-shield"></i>
                            <span class="nav-text">Empleados</span>
                        </a>
                    @endif
                </li>
                <li class="nav-item">
                    @if (auth()->user()->role?->code === 'admin')
                        <a href="{{ route('empresas.index') }}" class="nav-link {{ request()->routeIs('empresas.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-building"></i>
                            <span class="nav-text">Empresa</span>
                        </a>
                    @endif
                </li>
                <li class="nav-item">
                    @if (auth()->user()->role?->code === 'admin')
                        <a href="{{ route('parametrizacion.index') }}" class="nav-link {{ request()->routeIs('parametrizacion.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-building-columns"></i>
                            <span class="nav-text">Parametrización DIAN</span>
                        </a>
                    @endif    
                </li>
                <li class="nav-item sidebar-heading" title="Reportes">
                    <span class="heading-text">Reportes</span>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa-solid fa-chart-line"></i>
                        <span class="nav-text">Ventas y Rentabilidad</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-user p-2 border-top border-light border-opacity-10 mt-auto">
                <div class="d-flex align-items-center gap-2 p-2 rounded-3 user-card-inner" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1);">
                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold fs-6 shadow-sm user-avatar" style="width: 38px; height: 38px; min-width: 38px; background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);">
                        {{ $usuario_inicial }}
                    </div>
                    <div class="overflow-hidden flex-grow-1 user-info-text">
                        <h6 class="text-white mb-0 fw-bold text-truncate" style="font-size: 0.85rem;">{{ $usuario_nombre }}</h6>
                        <span class="text-white-50 d-block text-truncate" style="font-size: 0.7rem;"><i class="fa-solid fa-circle text-success" style="font-size: 0.5rem; vertical-align: middle; margin-right: 3px;"></i>En línea</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="m-0 user-logout-form">
                        @csrf
                        <button type="submit" class="btn btn-sm border-0 p-1 rounded-circle d-flex align-items-center justify-content-center text-white-50 btn-logout-icon" title="Cerrar Sesión" style="width: 28px; height: 28px; background: transparent;">
                            <i class="fa-solid fa-power-off"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="app-main flex-grow-1 d-flex flex-column overflow-hidden">

            <!-- Topbar Original (Blanco con Breadcrumbs y Menú de Usuario) -->
            <header class="app-topbar d-flex justify-content-between align-items-center px-4 bg-white border-bottom shadow-sm z-1 flex-shrink-0" style="height: 80px; min-height: 80px;">
                <!-- Botón toggle sidebar móvil -->
                <button class="btn btn-light d-md-none me-2" id="toggleSidebar">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <!-- Breadcrumb dinámico -->
                <nav aria-label="breadcrumb" class="d-none d-md-flex align-items-center">
                    <ol class="breadcrumb mb-0 align-items-center">
                        <!-- Inicio siempre primero -->
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}"
                               class="text-decoration-none d-flex align-items-center gap-1"
                               style="color:#12102f;">
                                <i class="fa-solid fa-house" style="font-size:.8rem;"></i>
                                <span style="font-size:.83rem;">FactuWeb PRO</span>
                            </a>
                        </li>
                        @foreach ($breadcrumbs as $i => $crumb)
                            @php $is_last = ($i === count($breadcrumbs) - 1); @endphp
                            <li class="breadcrumb-item {{ $is_last ? 'active' : '' }}"
                                @if ($is_last) aria-current="page" @endif>
                                @if ($is_last)
                                    <span class="d-flex align-items-center gap-1 fw-semibold" style="color:#ea580c; font-size:.85rem;">
                                        <i class="fa-solid {{ $crumb['icono'] }}" style="font-size:.78rem;"></i>
                                        {{ $crumb['label'] }}
                                    </span>
                                @else
                                    <span class="text-muted d-flex align-items-center gap-1" style="font-size:.83rem;">
                                        <i class="fa-solid {{ $crumb['icono'] }}" style="font-size:.75rem;"></i>
                                        {{ $crumb['label'] }}
                                    </span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </nav>

                <!-- Perfil de usuario con Dropdown para Ver Perfil y Cerrar Sesión -->
                <div class="dropdown ms-auto">
                    <button class="btn btn-link text-decoration-none p-0 d-flex align-items-center gap-3 border-0"
                            type="button"
                            id="userTopDropdown"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                        <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm text-white fw-bold flex-shrink-0"
                             style="width:40px; height:40px; background:linear-gradient(135deg,#12102f,#ea580c); font-size:1rem;">
                            {{ $usuario_inicial }}
                        </div>
                        <div class="d-none d-md-block lh-sm text-start">
                            <div class="fw-semibold text-dark" style="font-size:.9rem;">{{ $usuario_nombre }}</div>
                            <span class="badge rounded-pill px-2 text-white" style="background-color:{{ $color_rol }}; font-size:.68rem;">{{ $nombre_rol }}</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-muted ms-1 d-none d-md-inline" style="font-size: 0.75rem;"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2 py-2"
                        aria-labelledby="userTopDropdown"
                        style="min-width: 220px;">
                        <li class="px-3 py-2 border-bottom mb-1 bg-light">
                            <span class="fw-bold text-dark d-block text-truncate" style="font-size: 0.88rem;">{{ $usuario_nombre }}</span>
                            <small class="text-muted d-block text-truncate" style="font-size: 0.75rem;">{{ $user?->email }}</small>
                            <span class="badge rounded-pill px-2 mt-1 text-white" style="background-color:{{ $color_rol }}; font-size: 0.68rem;">
                                {{ $nombre_rol }}
                            </span>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-secondary" href="{{ route('profile.edit') }}">
                                <i class="fa-solid fa-user-gear text-primary" style="width: 20px;"></i>
                                <span>Ver Perfil</span>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger">
                                    <i class="fa-solid fa-arrow-right-from-bracket" style="width: 20px;"></i>
                                    <span>Cerrar Sesión</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </header>

            <!-- Main Content Area (Scrollable) -->
            <main class="app-content p-4 bg-light-section flex-grow-1 overflow-auto">
                @yield('content')
            </main>

        </div>

    </div>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Script toggle sidebar móvil -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggleSidebar');

            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    sidebar.classList.toggle('show');
                });

                document.addEventListener('click', function (e) {
                    if (window.innerWidth <= 768 && sidebar.classList.contains('show')) {
                        if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                            sidebar.classList.remove('show');
                        }
                    }
                });
            }
        });
    </script>

    @stack('scripts')
    @yield('js')
</body>
</html>