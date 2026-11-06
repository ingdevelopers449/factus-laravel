@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Ventas y Rentabilidad - FactuWeb PRO')

@section('content')
<div class="container-fluid px-0">

    {{-- ENCABEZADO Y BOTONES DE ACCIÓN (OCULTOS EN IMPRESIÓN) --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 print-hide">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-pill px-3 py-1 text-white shadow-sm" style="background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); font-size: 0.8rem; letter-spacing: 0.5px;">
                    <i class="fa-solid fa-chart-line me-1"></i> REPORTES FINANCIEROS
                </span>
                <span class="badge rounded-pill bg-white text-secondary border px-3 py-1 shadow-sm small">
                    <i class="fa-solid fa-calendar-check text-warning me-1"></i> Período: 
                    <strong class="text-dark">{{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}</strong> al 
                    <strong class="text-dark">{{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</strong>
                </span>
            </div>
            <h3 class="fw-bold text-dark mb-0">Ventas y Rentabilidad</h3>
            <p class="text-muted small mb-0">Monitorea los ingresos netos, costos de adquisición de mercancía, utilidad bruta y margen comercial.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" onclick="window.print()" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3 shadow-sm">
                <i class="fa-solid fa-print"></i>
                <span class="d-none d-sm-inline">Imprimir Informe</span>
            </button>

            <a href="{{ route('facturas.create') }}" class="btn btn-primary-gradient d-inline-flex align-items-center gap-2 px-4 py-2 rounded-3 shadow-sm fw-bold">
                <i class="fa-solid fa-cart-plus"></i>
                <span>Nueva Venta (POS)</span>
            </a>
        </div>
    </div>

    {{-- BARRA DE FILTROS POR FECHA --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white print-hide">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('rentabilidad.index') }}" method="GET" id="filtroFechasForm" class="m-0">
                <div class="row g-3 align-items-end">

                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="form-label fw-semibold text-secondary small mb-1">
                            <i class="fa-solid fa-calendar-day text-warning me-1"></i> Fecha Inicial
                        </label>
                        <input
                            type="date"
                            name="fecha_inicio"
                            id="fecha_inicio"
                            class="form-control bg-light py-2"
                            value="{{ $fechaInicio }}"
                            required
                        >
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="form-label fw-semibold text-secondary small mb-1">
                            <i class="fa-solid fa-calendar-check text-warning me-1"></i> Fecha Final
                        </label>
                        <input
                            type="date"
                            name="fecha_fin"
                            id="fecha_fin"
                            class="form-control bg-light py-2"
                            value="{{ $fechaFin }}"
                            required
                        >
                    </div>

                    <div class="col-12 col-md-4 d-flex align-items-center gap-2">
                        <button type="submit" class="btn btn-primary-gradient px-4 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-sm" style="height: 42px;">
                            <i class="fa-solid fa-filter"></i>
                            <span>Filtrar</span>
                        </button>

                        <a href="{{ route('rentabilidad.index') }}" class="btn btn-outline-secondary px-3 py-2 d-inline-flex align-items-center gap-1" style="height: 42px;" title="Restablecer al mes actual">
                            <i class="fa-solid fa-arrow-rotate-left"></i>
                            <span class="d-none d-sm-inline">Mes Actual</span>
                        </a>
                    </div>

                    <div class="col-12 col-md-2 text-md-end">
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <button type="button" class="btn btn-light border btn-preset" onclick="setPreset('hoy')">Hoy</button>
                            <button type="button" class="btn btn-light border btn-preset" onclick="setPreset('7dias')">7d</button>
                            <button type="button" class="btn btn-light border btn-preset" onclick="setPreset('mes')">Mes</button>
                            <button type="button" class="btn btn-light border btn-preset" onclick="setPreset('anio')">Año</button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- 4 TARJETAS KPI EJECUTIVAS --}}
    <div class="row g-3 g-md-4 mb-4">

        {{-- 1. TOTAL INGRESOS VENTAS NETAS --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-uppercase small fw-bold text-muted" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                        Ventas Netas (Subtotal)
                    </span>
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-primary" style="width: 42px; height: 42px; background: rgba(59, 130, 246, 0.1);">
                        <i class="fa-solid fa-money-bill-trend-up fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bolder text-dark mb-1">${{ number_format($totalVentasNetas, 2, ',', '.') }}</h3>
                <small class="text-muted d-block mt-auto" style="font-size: 0.75rem;">
                    Total con IVA: <strong class="text-dark">${{ number_format($totalVentasConIva, 2, ',', '.') }}</strong>
                </small>
            </div>
        </div>

        {{-- 2. COSTO TOTAL DE MERCANCÍA VENDIDA (COGS) --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-uppercase small fw-bold text-muted" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                        Costo de Ventas (COGS)
                    </span>
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-secondary" style="width: 42px; height: 42px; background: rgba(100, 116, 139, 0.12);">
                        <i class="fa-solid fa-boxes-packing fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bolder text-dark mb-1">${{ number_format($costoTotal, 2, ',', '.') }}</h3>
                <small class="text-muted d-block mt-auto" style="font-size: 0.75rem;">
                    Costo de adquisición de productos vendidos
                </small>
            </div>
        </div>

        {{-- 3. GANANCIA BRUTA (UTILIDAD NETA COMERCIAL) - CARD EJECUTIVA OSCURA --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 p-md-4 text-white" style="background: linear-gradient(135deg, #0b1120 0%, #171033 100%); border: 1px solid rgba(245, 158, 11, 0.3) !important;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-uppercase small fw-bold text-warning" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                        <i class="fa-solid fa-sack-dollar me-1"></i> Ganancia Bruta (Utilidad)
                    </span>
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-warning" style="width: 42px; height: 42px; background: rgba(245, 158, 11, 0.15);">
                        <i class="fa-solid fa-coins fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bolder mb-1" style="color: #f59e0b; font-size: 1.85rem;">
                    ${{ number_format($gananciaBruta, 2, ',', '.') }}
                </h3>
                <small class="text-white-50 d-block mt-auto" style="font-size: 0.75rem;">
                    Ventas netas menos costos de mercancía
                </small>
            </div>
        </div>

        {{-- 4. MARGEN DE RENTABILIDAD GLOBAL --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-uppercase small fw-bold text-muted" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                        Margen de Rentabilidad
                    </span>
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-success" style="width: 42px; height: 42px; background: rgba(16, 185, 129, 0.1);">
                        <i class="fa-solid fa-percent fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-1">
                    <h3 class="fw-bolder text-dark mb-0">{{ number_format($margenPorcentaje, 1, ',', '.') }}%</h3>
                    @if ($margenPorcentaje >= 30)
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill small px-2">Saludable</span>
                    @elseif ($margenPorcentaje > 0)
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill small px-2">Moderado</span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary border rounded-pill small px-2">Sin datos</span>
                    @endif
                </div>
                <div class="progress mt-2 mb-1" style="height: 6px;">
                    <div class="progress-bar {{ $margenPorcentaje >= 30 ? 'bg-success' : ($margenPorcentaje > 0 ? 'bg-warning' : 'bg-secondary') }}"
                         role="progressbar"
                         style="width: {{ min(max($margenPorcentaje, 0), 100) }}%;"></div>
                </div>
                <small class="text-muted d-block mt-auto" style="font-size: 0.72rem;">
                    {{ $totalFacturas }} facturas | Ticket prom: ${{ number_format($ticketPromedio, 2, ',', '.') }}
                </small>
            </div>
        </div>

    </div>

    {{-- SECCIÓN DE GRÁFICOS INTERACTIVOS (CHART.JS) --}}
    <div class="row g-4 mb-4">

        {{-- GRÁFICO 1: EVOLUCIÓN TEMPORAL DE VENTAS VS COSTOS VS UTILIDAD --}}
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 p-2 text-warning d-flex align-items-center justify-content-center" style="background: rgba(245, 158, 11, 0.12); width: 36px; height: 36px;">
                            <i class="fa-solid fa-chart-column fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Evolución Financiera</h6>
                            <small class="text-muted">Comparativa de Ventas, Costos y Ganancia por día</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if ($tendenciaVentas->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fa-solid fa-chart-line display-4 text-warning opacity-25 mb-3 d-block"></i>
                            <h6 class="fw-bold text-dark">No hay registros en este rango de fechas</h6>
                            <p class="small text-muted mb-0">Selecciona otro período en el filtro superior para visualizar la tendencia.</p>
                        </div>
                    @else
                        <div style="height: 300px; position: relative;">
                            <canvas id="chartEvolucionFinanciera"></canvas>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- GRÁFICO 2: DISTRIBUCIÓN DE RENTABILIDAD POR CATEGORÍA --}}
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 p-2 text-warning d-flex align-items-center justify-content-center" style="background: rgba(245, 158, 11, 0.12); width: 36px; height: 36px;">
                            <i class="fa-solid fa-chart-pie fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Utilidad por Categoría</h6>
                            <small class="text-muted">Aporte porcentual al margen</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center">
                    @if ($rentabilidadCategorias->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fa-solid fa-pie-chart display-4 text-warning opacity-25 mb-3 d-block"></i>
                            <h6 class="fw-bold text-dark">Sin datos de categorías</h6>
                            <p class="small text-muted mb-0">No se encontraron ventas para categorizar en el período.</p>
                        </div>
                    @else
                        <div style="height: 250px; width: 100%; position: relative;">
                            <canvas id="chartCategoriasDona"></canvas>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- TABLA 1: RANKING DE PRODUCTOS CON MAYOR RENTABILIDAD --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-3 p-2 text-warning d-flex align-items-center justify-content-center" style="background: rgba(245, 158, 11, 0.12); width: 36px; height: 36px;">
                    <i class="fa-solid fa-medal fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Top 10 Productos Más Rentables</h6>
                    <small class="text-muted">Artículos que más ganancia bruta aportan a tu negocio</small>
                </div>
            </div>
            <span class="badge rounded-pill bg-light text-secondary border px-3 py-2 fw-semibold">
                {{ $topProductos->count() }} productos listados
            </span>
        </div>

        <div class="table-responsive m-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-secondary small fw-bold text-uppercase" style="letter-spacing: 0.5px;">
                        <th class="ps-4 py-3" style="width: 40px;">#</th>
                        <th class="py-3">Producto / Categoría</th>
                        <th class="text-center py-3" style="width: 120px;">Unidades</th>
                        <th class="text-end py-3" style="width: 140px;">Total Ventas</th>
                        <th class="text-end py-3" style="width: 140px;">Costo Total</th>
                        <th class="text-end py-3" style="width: 150px;">Ganancia ($)</th>
                        <th class="text-center pe-4 py-3" style="width: 140px;">Margen (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($topProductos as $index => $item)
                        <tr>
                            <td class="ps-4 py-3">
                                @if ($index === 0)
                                    <span class="badge rounded-circle p-2 text-white bg-warning shadow-sm"><i class="fa-solid fa-trophy"></i></span>
                                @elseif ($index === 1)
                                    <span class="badge rounded-circle p-2 text-white bg-secondary shadow-sm"><i class="fa-solid fa-medal"></i></span>
                                @elseif ($index === 2)
                                    <span class="badge rounded-circle p-2 text-white bg-bronze shadow-sm" style="background-color: #cd7f32;"><i class="fa-solid fa-award"></i></span>
                                @else
                                    <span class="fw-bold text-muted">{{ $index + 1 }}</span>
                                @endif
                            </td>

                            <td class="py-3">
                                <span class="fw-bold text-dark d-block" style="font-size: 0.95rem;">
                                    {{ $item->producto }}
                                </span>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span class="badge bg-light text-muted border small px-2 py-0">
                                        {{ $item->categoria }}
                                    </span>
                                    @if ($item->codigo_barras)
                                        <small class="text-muted font-monospace"><i class="fa-solid fa-barcode me-1"></i>{{ $item->codigo_barras }}</small>
                                    @endif
                                </div>
                            </td>

                            <td class="text-center py-3">
                                <span class="badge rounded-pill bg-light text-dark border px-3 py-2 fw-bold" style="font-size: 0.88rem;">
                                    {{ number_format($item->cantidad_vendida, 0) }}
                                </span>
                            </td>

                            <td class="text-end py-3 text-secondary">
                                ${{ number_format($item->ingresos_ventas, 2, ',', '.') }}
                            </td>

                            <td class="text-end py-3 text-muted">
                                ${{ number_format($item->costo_total, 2, ',', '.') }}
                            </td>

                            <td class="text-end py-3 fw-bold text-dark" style="color: #ea580c !important; font-size: 0.98rem;">
                                +${{ number_format($item->ganancia_bruta, 2, ',', '.') }}
                            </td>

                            <td class="text-center pe-4 py-3">
                                <span class="fw-bold d-block text-success" style="font-size: 0.92rem;">
                                    {{ number_format($item->margen_porcentaje, 1) }}%
                                </span>
                                <div class="progress mt-1 mx-auto" style="height: 4px; width: 80px;">
                                    <div class="progress-bar bg-success" style="width: {{ min(max($item->margen_porcentaje, 0), 100) }}%;"></div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-boxes-packing fs-2 text-warning opacity-50 mb-2 d-block"></i>
                                <span class="fw-bold text-dark d-block">Sin ventas de productos registradas</span>
                                <small>No hay datos suficientes para calcular el ranking de rentabilidad en este período.</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- TABLA 2: HISTORIAL DE FACTURAS CON MARGEN EN EL PERÍODO --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-3 p-2 text-warning d-flex align-items-center justify-content-center" style="background: rgba(245, 158, 11, 0.12); width: 36px; height: 36px;">
                    <i class="fa-solid fa-file-invoice-dollar fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Detalle de Ventas Facturadas</h6>
                    <small class="text-muted">Margen y utilidad por cada factura emitida</small>
                </div>
            </div>
            <span class="badge rounded-pill bg-light text-secondary border px-3 py-2 fw-semibold">
                {{ $facturas->total() }} facturas encontradas
            </span>
        </div>

        <div class="table-responsive m-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-secondary small fw-bold text-uppercase" style="letter-spacing: 0.5px;">
                        <th class="ps-4 py-3">Factura</th>
                        <th class="py-3">Fecha</th>
                        <th class="py-3">Cliente</th>
                        <th class="text-end py-3">Total Venta</th>
                        <th class="text-end py-3">Costo Est.</th>
                        <th class="text-end py-3">Utilidad ($)</th>
                        <th class="text-center py-3">Margen (%)</th>
                        <th class="text-center pe-4 py-3" style="width: 90px;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($facturas as $fac)
                        @php
                            $costoFac = $fac->detalles->sum(function ($det) {
                                return $det->cantidad * (float) ($det->precio_unitario_costo ?? 0);
                            });
                            $utilidadFac = (float) $fac->subtotal - $costoFac;
                            $margenFac = (float) $fac->subtotal > 0 ? ($utilidadFac / (float) $fac->subtotal) * 100 : 0;
                        @endphp
                        <tr>
                            <td class="ps-4 py-3 font-monospace fw-bold text-dark">
                                {{ $fac->prefijo_resolucion }}-{{ $fac->consecutivo }}
                            </td>

                            <td class="py-3 text-secondary small">
                                {{ \Carbon\Carbon::parse($fac->fecha_emision)->format('d/m/Y h:i A') }}
                            </td>

                            <td class="py-3 text-dark fw-medium">
                                {{ $fac->cliente?->nombre_razon_social ?? 'Consumidor Final' }}
                            </td>

                            <td class="text-end py-3 fw-bold text-dark">
                                ${{ number_format($fac->total_pagar, 2, ',', '.') }}
                            </td>

                            <td class="text-end py-3 text-muted small">
                                ${{ number_format($costoFac, 2, ',', '.') }}
                            </td>

                            <td class="text-end py-3 fw-bold {{ $utilidadFac >= 0 ? 'text-success' : 'text-danger' }}">
                                +${{ number_format($utilidadFac, 2, ',', '.') }}
                            </td>

                            <td class="text-center py-3">
                                <span class="badge {{ $margenFac >= 30 ? 'bg-success-subtle text-success border border-success-subtle' : ($margenFac > 0 ? 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' : 'bg-secondary-subtle text-secondary') }} rounded-pill px-2 py-1">
                                    {{ number_format($margenFac, 1) }}%
                                </span>
                            </td>

                            <td class="text-center pe-4 py-3">
                                <a href="{{ route('facturas.show', $fac->id) }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" title="Ver detalle de factura">
                                    <i class="fa-solid fa-eye"></i>
                                    <span class="d-none d-md-inline">Ver</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-file-circle-xmark fs-2 text-warning opacity-50 mb-2 d-block"></i>
                                <span class="fw-bold text-dark d-block">No se registraron ventas en el período</span>
                                <small>Ajusta las fechas para consultar otros rangos.</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($facturas->hasPages())
            <div class="card-footer bg-white py-3 px-4 border-top d-flex justify-content-center">
                {{ $facturas->links() }}
            </div>
        @endif
    </div>

</div>

{{-- SCRIPT PARA CHART.JS Y PRESETS RÁPIDOS DE FECHA --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Presets rápidos de filtros de fecha
    function setPreset(tipo) {
        const hoy = new Date();
        const formatDate = (date) => {
            const yyyy = date.getFullYear();
            const mm = String(date.getMonth() + 1).padStart(2, '0');
            const dd = String(date.getDate()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd}`;
        };

        const fechaFinInput = document.getElementById('fecha_fin');
        const fechaInicioInput = document.getElementById('fecha_inicio');

        fechaFinInput.value = formatDate(hoy);

        if (tipo === 'hoy') {
            fechaInicioInput.value = formatDate(hoy);
        } else if (tipo === '7dias') {
            const fecha7 = new Date();
            fecha7.setDate(hoy.getDate() - 7);
            fechaInicioInput.value = formatDate(fecha7);
        } else if (tipo === 'mes') {
            const primerDiaMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
            fechaInicioInput.value = formatDate(primerDiaMes);
        } else if (tipo === 'anio') {
            const primerDiaAnio = new Date(hoy.getFullYear(), 0, 1);
            fechaInicioInput.value = formatDate(primerDiaAnio);
        }

        document.getElementById('filtroFechasForm').submit();
    }

    // Inicialización de Gráficos Chart.js
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Gráfico de Evolución Financiera
        const canvasEvolucion = document.getElementById('chartEvolucionFinanciera');
        if (canvasEvolucion) {
            const datosEvolucion = @json($tendenciaVentas);
            const labels = datosEvolucion.map(item => item.fecha);
            const ventas = datosEvolucion.map(item => parseFloat(item.ventas));
            const costos = datosEvolucion.map(item => parseFloat(item.costos));
            const ganancia = datosEvolucion.map(item => parseFloat(item.ganancia));

            new Chart(canvasEvolucion, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Ventas Netas ($)',
                            data: ventas,
                            backgroundColor: 'rgba(59, 130, 246, 0.7)',
                            borderColor: '#3b82f6',
                            borderWidth: 1,
                            borderRadius: 6
                        },
                        {
                            label: 'Costo Mercancía ($)',
                            data: costos,
                            backgroundColor: 'rgba(100, 116, 139, 0.5)',
                            borderColor: '#64748b',
                            borderWidth: 1,
                            borderRadius: 6
                        },
                        {
                            label: 'Utilidad Bruta ($)',
                            data: ganancia,
                            type: 'line',
                            backgroundColor: 'rgba(245, 158, 11, 0.2)',
                            borderColor: '#f59e0b',
                            borderWidth: 3,
                            pointBackgroundColor: '#ea580c',
                            pointRadius: 4,
                            fill: true,
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: { family: 'Plus Jakarta Sans', size: 12, weight: '600' }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': $' + Number(context.raw).toLocaleString('es-CO', { minimumFractionDigits: 2 });
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
        const canvasCategorias = document.getElementById('chartCategoriasDona');
        if (canvasCategorias) {
            const datosCategorias = @json($rentabilidadCategorias);
            const catLabels = datosCategorias.map(item => item.categoria);
            const catData = datosCategorias.map(item => parseFloat(item.ganancia_bruta));

            new Chart(canvasCategorias, {
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
                            '#8b5cf6',
                            '#ec4899',
                            '#64748b'
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
                    cutout: '65%'
                }
            });
        }
    });
</script>
@endpush

{{-- ESTILOS DE IMPRESIÓN --}}
@section('css')
<style>
    @media print {
        .app-sidebar,
        .app-topbar,
        .print-hide,
        nav,
        header {
            display: none !important;
        }

        body, .admin-body, .app-main, .app-content {
            background: #ffffff !important;
            padding: 0 !important;
            margin: 0 !important;
            overflow: visible !important;
        }

        .card {
            box-shadow: none !important;
            border: 1px solid #e2e8f0 !important;
            break-inside: avoid;
        }
    }
</style>
@endsection
@endsection

