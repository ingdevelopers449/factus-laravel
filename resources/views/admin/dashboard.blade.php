@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Panel de Control - FactuWeb PRO')

@section('content')
<div class="container-fluid px-0">

    {{-- 1. HERO HEADER DE BIENVENIDA Y ACCIONES RÁPIDAS --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-pill px-3 py-1 text-white shadow-sm" style="background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); font-size: 0.8rem; letter-spacing: 0.5px;">
                    <i class="fa-solid fa-gauge-high me-1"></i> PANEL PRINCIPAL
                </span>
                <span class="badge rounded-pill bg-white text-secondary border px-3 py-1 shadow-sm small">
                    <i class="fa-solid fa-calendar-day text-warning me-1"></i> {{ now()->translatedFormat('l, d \\d\\e F \\d\\e Y') }}
                </span>
            </div>
            <h3 class="fw-bold text-dark mb-0">Hola, {{ auth()->user()->name }} 👋</h3>
            <p class="text-muted small mb-0">Bienvenido al centro de operaciones y facturación electrónica de FactuWeb PRO.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('rentabilidad.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3 shadow-sm">
                <i class="fa-solid fa-chart-line"></i>
                <span class="d-none d-sm-inline">Rentabilidad</span>
            </a>

            <a href="{{ route('facturas.create') }}" class="btn btn-primary-gradient d-inline-flex align-items-center gap-2 px-4 py-2 rounded-3 shadow-sm fw-bold">
                <i class="fa-solid fa-cash-register"></i>
                <span>Abrir Caja (POS)</span>
            </a>
        </div>
    </div>

    {{-- 2. BANNER DE ESTADO DIAN Y CAJA --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="card-body p-3 p-md-4">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-4 border-end-md">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center text-success flex-shrink-0" style="width: 44px; height: 44px; background: rgba(16, 185, 129, 0.12);">
                            <i class="fa-solid fa-circle-dot fs-5"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase d-block" style="font-size: 0.72rem; letter-spacing: 0.5px;">Estado del Sistema</span>
                            <h6 class="fw-bold text-dark mb-0">Caja POS Operativa</h6>
                            <small class="text-success"><i class="fa-solid fa-shield-check me-1"></i>Conexión en línea</small>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-8">
                    @if ($resolucionActiva)
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-1">
                            <div>
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0 me-1 small">
                                    DIAN {{ $resolucionActiva->prefijo }}
                                </span>
                                <strong class="text-dark small">Res. N° {{ $resolucionActiva->numero_resolucion }}</strong>
                                <span class="text-muted small">({{ number_format($resolucionActiva->rango_inicial, 0) }} al {{ number_format($resolucionActiva->rango_final, 0) }})</span>
                            </div>
                            <span class="text-muted small">
                                Folios: <strong>{{ number_format($resolucionActiva->contador_actual, 0) }}</strong> usados ({{ $porcentajeDian }}%)
                            </span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar {{ $porcentajeDian > 85 ? 'bg-danger' : ($porcentajeDian > 60 ? 'bg-warning' : 'bg-success') }}"
                                 role="progressbar"
                                 style="width: {{ $porcentajeDian }}%;"></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small class="text-muted" style="font-size: 0.72rem;">
                                @if (!is_null($diasVigenciaDian))
                                    Vigencia: {{ $diasVigenciaDian > 0 ? "Quedan {$diasVigenciaDian} días" : 'Resolución vencida' }}
                                @endif
                            </small>
                            <a href="{{ route('parametrizacion.index') }}" class="text-decoration-none small text-warning fw-semibold" style="font-size: 0.75rem;">
                                Administrar <i class="fa-solid fa-arrow-right fa-2xs"></i>
                            </a>
                        </div>
                    @else
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small"><i class="fa-solid fa-triangle-exclamation text-warning me-1"></i> No hay una Resolución DIAN activa configurada.</span>
                            <a href="{{ route('parametrizacion.index') }}" class="btn btn-sm btn-outline-warning">Configurar DIAN</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- 3. FILA DE 4 TARJETAS KPI PRINCIPALES --}}
    <div class="row g-3 g-md-4 mb-4">

        {{-- KPI 1: VENTAS DE HOY --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-uppercase small fw-bold text-muted" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                        Ventas de Hoy
                    </span>
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-primary" style="width: 42px; height: 42px; background: rgba(59, 130, 246, 0.1);">
                        <i class="fa-solid fa-calendar-day fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bolder text-dark mb-1">${{ number_format($ventasHoy, 2, ',', '.') }}</h3>
                <small class="text-muted d-block mt-auto" style="font-size: 0.75rem;">
                    <i class="fa-solid fa-receipt text-primary me-1"></i> <strong>{{ $facturasHoyCount }}</strong> {{ $facturasHoyCount === 1 ? 'venta emitida hoy' : 'ventas emitidas hoy' }}
                </small>
            </div>
        </div>

        {{-- KPI 2: VENTAS DEL MES ACTUAL --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-uppercase small fw-bold text-muted" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                        Facturación del Mes
                    </span>
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-warning" style="width: 42px; height: 42px; background: rgba(245, 158, 11, 0.12);">
                        <i class="fa-solid fa-file-invoice-dollar fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bolder text-dark mb-1">${{ number_format($ventasMes, 2, ',', '.') }}</h3>
                <small class="text-muted d-block mt-auto" style="font-size: 0.75rem;">
                    Subtotal neto: <strong>${{ number_format($ventasNetasMes, 2, ',', '.') }}</strong> ({{ $facturasMesCount }} facturas)
                </small>
            </div>
        </div>

        {{-- KPI 3: UTILIDAD BRUTA DEL MES (CARD OSCURA DESTACADA) --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 p-md-4 text-white" style="background: linear-gradient(135deg, #0b1120 0%, #171033 100%); border: 1px solid rgba(245, 158, 11, 0.3) !important;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-uppercase small fw-bold text-warning" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                        <i class="fa-solid fa-sack-dollar me-1"></i> Ganancia Neta del Mes
                    </span>
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-warning" style="width: 42px; height: 42px; background: rgba(245, 158, 11, 0.15);">
                        <i class="fa-solid fa-coins fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bolder mb-1" style="color: #f59e0b; font-size: 1.85rem;">
                    ${{ number_format($gananciaMes, 2, ',', '.') }}
                </h3>
                <small class="text-white-50 d-block mt-auto" style="font-size: 0.75rem;">
                    Margen mensual: <strong class="text-warning">{{ number_format($margenMes, 1) }}%</strong>
                </small>
            </div>
        </div>

        {{-- KPI 4: CLIENTES Y CATÁLOGO --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-uppercase small fw-bold text-muted" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                        Catálogo & Clientes
                    </span>
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-success" style="width: 42px; height: 42px; background: rgba(16, 185, 129, 0.1);">
                        <i class="fa-solid fa-boxes-stacked fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <div>
                        <h4 class="fw-bolder text-dark mb-0">{{ $totalProductos }}</h4>
                        <small class="text-muted" style="font-size: 0.72rem;">Productos</small>
                    </div>
                    <div class="vr mx-2" style="height: 32px;"></div>
                    <div>
                        <h4 class="fw-bolder text-dark mb-0">{{ $totalClientes }}</h4>
                        <small class="text-muted" style="font-size: 0.72rem;">Clientes</small>
                    </div>
                    <div class="vr mx-2" style="height: 32px;"></div>
                    <div>
                        <h4 class="fw-bolder {{ $conteoStockBajo > 0 ? 'text-danger' : 'text-success' }} mb-0">{{ $conteoStockBajo }}</h4>
                        <small class="text-muted" style="font-size: 0.72rem;">Stock Crítico</small>
                    </div>
                </div>
                <small class="text-muted d-block mt-auto" style="font-size: 0.72rem;">
                    <i class="fa-solid fa-users text-success me-1"></i> Base comercial activa
                </small>
            </div>
        </div>

    </div>

    {{-- 4. GRÁFICOS INTERACTIVOS (CHART.JS) --}}
    <div class="row g-4 mb-4">

        {{-- GRÁFICO 1: EVOLUCIÓN DE VENTAS (ÚLTIMOS 7 DÍAS) --}}
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 p-2 text-warning d-flex align-items-center justify-content-center" style="background: rgba(245, 158, 11, 0.12); width: 36px; height: 36px;">
                            <i class="fa-solid fa-chart-line fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Actividad de Ventas (Últimos 7 Días)</h6>
                            <small class="text-muted">Total facturado diariamente</small>
                        </div>
                    </div>
                    <a href="{{ route('facturas.index') }}" class="btn btn-sm btn-link text-warning text-decoration-none fw-semibold">
                        Ver Facturas <i class="fa-solid fa-arrow-right fa-2xs"></i>
                    </a>
                </div>
                <div class="card-body p-4">
                    <div style="height: 280px; position: relative;">
                        <canvas id="chartVentas7Dias"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- GRÁFICO 2: TOP CATEGORÍAS VENDIDAS --}}
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 p-2 text-warning d-flex align-items-center justify-content-center" style="background: rgba(245, 158, 11, 0.12); width: 36px; height: 36px;">
                            <i class="fa-solid fa-chart-pie fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Ventas por Categoría</h6>
                            <small class="text-muted">Distribución de ingresos</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center">
                    @if ($chartCategoriasData->isEmpty() || $chartCategoriasData->sum() == 0)
                        <div class="text-center py-5 text-muted">
                            <i class="fa-solid fa-chart-pie display-4 text-warning opacity-25 mb-3 d-block"></i>
                            <h6 class="fw-bold text-dark">Sin ventas para categorizar</h6>
                            <small class="text-muted">Las categorías aparecerán a medida que emitas facturas.</small>
                        </div>
                    @else
                        <div style="height: 240px; width: 100%; position: relative;">
                            <canvas id="chartCategorias"></canvas>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- 5. FILA INFERIOR: ÚLTIMAS FACTURAS Y GESTIÓN DE STOCK CRÍTICO --}}
    <div class="row g-4">

        {{-- ÚLTIMAS FACTURAS EMITIDAS --}}
        <div class="col-12 col-xl-7">
            <div class="card border-0 shadow-sm rounded-4 bg-white h-100 overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 p-2 text-warning d-flex align-items-center justify-content-center" style="background: rgba(245, 158, 11, 0.12); width: 36px; height: 36px;">
                            <i class="fa-solid fa-receipt fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Últimas Facturas Emitidas</h6>
                            <small class="text-muted">Transacciones más recientes en el sistema</small>
                        </div>
                    </div>
                    <a href="{{ route('facturas.index') }}" class="btn btn-sm btn-outline-secondary px-3 py-1 rounded-3">
                        Ver Todas
                    </a>
                </div>

                <div class="table-responsive m-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-secondary small fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.72rem;">
                                <th class="ps-4 py-3">Factura</th>
                                <th class="py-3">Cliente</th>
                                <th class="text-end py-3">Total</th>
                                <th class="text-center py-3">Estado DIAN</th>
                                <th class="text-center pe-4 py-3" style="width: 70px;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ultimasFacturas as $fac)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <span class="fw-bold text-dark font-monospace d-block">
                                            {{ $fac->prefijo_resolucion }}-{{ $fac->consecutivo }}
                                        </span>
                                        <small class="text-muted" style="font-size: 0.72rem;">
                                            {{ \Carbon\Carbon::parse($fac->fecha_emision)->format('d/m/Y h:i A') }}
                                        </small>
                                    </td>

                                    <td class="py-3">
                                        <span class="fw-medium text-dark d-block text-truncate" style="max-width: 180px;">
                                            {{ $fac->cliente?->nombre_razon_social ?? 'Consumidor Final' }}
                                        </span>
                                        <small class="text-muted" style="font-size: 0.72rem;">
                                            {{ $fac->detalles->count() }} {{ $fac->detalles->count() === 1 ? 'producto' : 'productos' }}
                                        </small>
                                    </td>

                                    <td class="text-end py-3 fw-bold text-dark">
                                        ${{ number_format($fac->total_pagar, 2, ',', '.') }}
                                    </td>

                                    <td class="text-center py-3">
                                        @if ($fac->estado_dian === 'aceptada')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">
                                                <i class="fa-solid fa-circle-check me-1"></i> Aceptada
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-1 small">
                                                <i class="fa-solid fa-clock me-1"></i> {{ ucfirst($fac->estado_dian) }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-center pe-4 py-3">
                                        <a href="{{ route('facturas.show', $fac->id) }}" class="btn btn-sm btn-outline-primary border-0 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Ver factura completa">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        No hay facturas emitidas todavía.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- STOCK BAJO Y ACCESOS RÁPIDOS --}}
        <div class="col-12 col-xl-5">

            {{-- TARJETA DE PRODUCTOS CON STOCK BAJO --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 p-2 text-danger d-flex align-items-center justify-content-center" style="background: rgba(239, 68, 68, 0.12); width: 36px; height: 36px;">
                            <i class="fa-solid fa-triangle-exclamation fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Alerta de Stock Crítico</h6>
                            <small class="text-muted">Productos con 5 o menos unidades</small>
                        </div>
                    </div>
                    <span class="badge {{ $conteoStockBajo > 0 ? 'bg-danger text-white' : 'bg-success text-white' }} rounded-pill px-2 py-1 small">
                        {{ $conteoStockBajo }} alerta(s)
                    </span>
                </div>

                <div class="card-body p-0">
                    @if ($productosStockBajo->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="fa-solid fa-boxes-stacked fs-3 text-success opacity-50 mb-2 d-block"></i>
                            <span class="fw-bold text-dark d-block">¡Inventario Saludable!</span>
                            <small>No hay productos con nivel de stock crítico.</small>
                        </div>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach ($productosStockBajo as $prod)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3">
                                    <div>
                                        <strong class="text-dark d-block" style="font-size: 0.9rem;">{{ $prod->producto }}</strong>
                                        <small class="text-muted">{{ $prod->categoria?->categoria ?? 'General' }} | ${{ number_format($prod->precio_venta, 2, ',', '.') }}</small>
                                    </div>
                                    <span class="badge {{ $prod->stock <= 2 ? 'bg-danger' : 'bg-warning text-dark' }} rounded-pill px-3 py-2 fw-bold">
                                        {{ $prod->stock }} un.
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            {{-- TARJETA DE ACCESOS RÁPIDOS OPERATIVOS --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <h6 class="fw-bold text-dark mb-3">Accesos Directos</h6>
                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('facturas.create') }}" class="btn btn-light border w-100 p-3 text-start rounded-3 h-100 d-flex flex-column justify-content-between hover-lift">
                            <i class="fa-solid fa-cash-register fs-4 text-warning mb-2"></i>
                            <div>
                                <strong class="d-block text-dark small">Punto de Venta</strong>
                                <small class="text-muted" style="font-size: 0.72rem;">Facturar caja POS</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('producto.index') }}" class="btn btn-light border w-100 p-3 text-start rounded-3 h-100 d-flex flex-column justify-content-between hover-lift">
                            <i class="fa-solid fa-box-open fs-4 text-primary mb-2"></i>
                            <div>
                                <strong class="d-block text-dark small">Catálogo</strong>
                                <small class="text-muted" style="font-size: 0.72rem;">Inventario y precios</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('clientes.index') }}" class="btn btn-light border w-100 p-3 text-start rounded-3 h-100 d-flex flex-column justify-content-between hover-lift">
                            <i class="fa-solid fa-users fs-4 text-success mb-2"></i>
                            <div>
                                <strong class="d-block text-dark small">Clientes</strong>
                                <small class="text-muted" style="font-size: 0.72rem;">Gestión de adquirentes</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('rentabilidad.index') }}" class="btn btn-light border w-100 p-3 text-start rounded-3 h-100 d-flex flex-column justify-content-between hover-lift">
                            <i class="fa-solid fa-chart-line fs-4 text-danger mb-2"></i>
                            <div>
                                <strong class="d-block text-dark small">Rentabilidad</strong>
                                <small class="text-muted" style="font-size: 0.72rem;">Márgenes y finanzas</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

{{-- SCRIPTS PARA INICIALIZAR CHART.JS EN EL DASHBOARD --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Gráfico de Actividad de Ventas (Últimos 7 Días)
        const canvas7Dias = document.getElementById('chartVentas7Dias');
        if (canvas7Dias) {
            const labels7 = @json($chart7DiasLabels);
            const data7 = @json($chart7DiasData);

            const ctx = canvas7Dias.getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 250);
            gradient.addColorStop(0, 'rgba(245, 158, 11, 0.4)');
            gradient.addColorStop(1, 'rgba(245, 158, 11, 0.0)');

            new Chart(canvas7Dias, {
                type: 'line',
                data: {
                    labels: labels7,
                    datasets: [{
                        label: 'Ventas ($ COP)',
                        data: data7,
                        backgroundColor: gradient,
                        borderColor: '#ea580c',
                        borderWidth: 3,
                        pointBackgroundColor: '#f59e0b',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        fill: true,
                        tension: 0.35
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Total: $' + Number(context.raw).toLocaleString('es-CO', { minimumFractionDigits: 2 });
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + Number(value).toLocaleString('es-CO');
                                }
                            }
                        }
                    }
                }
            });
        }

        // 2. Gráfico de Categorías (Dona)
        const canvasCat = document.getElementById('chartCategorias');
        if (canvasCat) {
            const catLabels = @json($chartCategoriasLabels);
            const catData = @json($chartCategoriasData);

            new Chart(canvasCat, {
                type: 'doughnut',
                data: {
                    labels: catLabels,
                    datasets: [{
                        data: catData,
                        backgroundColor: [
                            '#f59e0b',
                            '#ea580c',
                            '#3b82f6',
                            '#10b981',
                            '#8b5cf6'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                font: { family: 'Plus Jakarta Sans', size: 11, weight: '500' }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': $' + Number(context.raw).toLocaleString('es-CO', { minimumFractionDigits: 2 });
                                }
                            }
                        }
                    },
                    cutout: '68%'
                }
            });
        }
    });
</script>
@endpush

@section('css')
<style>
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
</style>
@endsection
@endsection
