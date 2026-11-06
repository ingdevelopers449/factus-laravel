@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Nueva Factura POS - FactuWeb PRO')

@section('content')
@php
    $detalles = $borrador->detalles ?? collect();
    $subtotalCalculado = $detalles->sum(function ($det) {
        return $det->cantidad * ($det->producto?->precio_venta ?? 0);
    });
    $totalArticulos = $detalles->sum('cantidad');
@endphp

<div class="container-fluid px-0">

    {{-- ENCABEZADO DE LA VISTA POS --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-pill px-3 py-1 text-white shadow-sm" style="background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); font-size: 0.8rem; letter-spacing: 0.5px;">
                    <i class="fa-solid fa-cash-register me-1"></i> TERMINAL POS
                </span>
                <span class="badge rounded-pill bg-white text-secondary border px-3 py-1 shadow-sm small">
                    <i class="fa-solid fa-user-tie text-warning me-1"></i> Cajero: <strong class="text-dark">{{ auth()->user()->name }}</strong>
                </span>
            </div>
            <h3 class="fw-bold text-dark mb-0">Facturación y Punto de Venta</h3>
            <p class="text-muted small mb-0">Registra y emite comprobantes electrónicos de manera ágil.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if (session('success'))
                <div class="alert alert-success py-2 px-3 mb-0 rounded-3 shadow-sm d-flex align-items-center gap-2 border-0 small">
                    <i class="fa-solid fa-circle-check fs-6"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <a href="{{ route('facturas.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3 shadow-sm">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Historial de Facturas</span>
            </a>
        </div>
    </div>

    {{-- LAYOUT PRINCIPAL EN 2 COLUMNAS (8 COLS PRODUCTOS / 4 COLS COBRO Y CLIENTE) --}}
    <div class="row g-4 align-items-start">

        {{-- COLUMNA IZQUIERDA: AGREGAR PRODUCTOS + TABLA DEL DETALLE --}}
        <div class="col-12 col-lg-8">

            {{-- 1. TARJETA: BUSCADOR Y AGREGADO RÁPIDO DE PRODUCTOS --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-3 p-2 text-warning d-flex align-items-center justify-content-center" style="background: rgba(245, 158, 11, 0.12); width: 36px; height: 36px;">
                                <i class="fa-solid fa-barcode fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Agregar Productos</h6>
                                <small class="text-muted">Busca por nombre o selecciona el ítem a facturar</small>
                            </div>
                        </div>
                        <span class="badge bg-light text-secondary border px-2 py-1 small">
                            <i class="fa-solid fa-boxes-stacked me-1 text-warning"></i> Catálogo Activo
                        </span>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('facturas.agregarProducto') }}" method="POST" class="m-0">
                        @csrf
                        <div class="row g-3 align-items-end">

                            {{-- Selector de Producto --}}
                            <div class="col-12 col-md-7">
                                <label class="form-label fw-semibold text-secondary small mb-1">
                                    Producto a facturar <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i class="fa-solid fa-magnifying-glass text-warning"></i>
                                    </span>
                                    <select name="id_producto" class="form-select bg-light border-start-0 py-2 fw-medium" required autofocus>
                                        <option value="">Buscar o seleccionar producto...</option>
                                        @foreach ($producto as $produc)
                                            <option value="{{ $produc->id }}" @selected(old('id_producto') == $produc->id)>
                                                {{ $produc->producto }} — ${{ number_format($produc->precio_venta, 2, ',', '.') }} (Stock: {{ $produc->stock }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Cantidad --}}
                            <div class="col-6 col-md-2">
                                <label class="form-label fw-semibold text-secondary small mb-1">
                                    Cantidad <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="number"
                                    name="cantidad"
                                    class="form-control bg-light py-2 text-center fw-bold text-dark"
                                    min="1"
                                    value="{{ old('cantidad', 1) }}"
                                    required
                                >
                            </div>

                            {{-- Botón Agregar --}}
                            <div class="col-6 col-md-3">
                                <button type="submit" class="btn btn-primary-gradient w-100 py-2 fw-bold d-inline-flex align-items-center justify-content-center gap-2 shadow-sm" style="height: 42px;">
                                    <i class="fa-solid fa-cart-plus fs-6"></i>
                                    <span>Agregar</span>
                                </button>
                            </div>

                        </div>
                    </form>

                    {{-- ALERTA DE ERRORES --}}
                    @if ($errors->has('cantidad') || $errors->has('id_producto'))
                        <div class="mt-3">
                            @if ($errors->has('cantidad'))
                                <div class="alert alert-danger py-2 px-3 mb-2 rounded-3 border-0 shadow-sm d-flex align-items-center gap-2 small">
                                    <i class="fa-solid fa-circle-exclamation text-danger fs-6"></i>
                                    <div>{{ $errors->first('cantidad') }}</div>
                                </div>
                            @endif

                            @if ($errors->has('id_producto'))
                                <div class="alert alert-danger py-2 px-3 mb-0 rounded-3 border-0 shadow-sm d-flex align-items-center gap-2 small">
                                    <i class="fa-solid fa-circle-exclamation text-danger fs-6"></i>
                                    <div>{{ $errors->first('id_producto') }}</div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- 2. TARJETA: TABLA DE DETALLES DEL CARRITO ACTUAL --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 p-2 text-warning d-flex align-items-center justify-content-center" style="background: rgba(245, 158, 11, 0.12); width: 36px; height: 36px;">
                            <i class="fa-solid fa-cart-shopping fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Productos en la Venta</h6>
                            <small class="text-muted">Lista de ítems listos para facturar</small>
                        </div>
                    </div>
                    <span class="badge rounded-pill bg-light text-secondary border px-3 py-2 fw-semibold">
                        <i class="fa-solid fa-list-check text-warning me-1"></i>
                        {{ $detalles->count() }} {{ $detalles->count() === 1 ? 'ítem' : 'ítems' }}
                    </span>
                </div>

                <div class="table-responsive m-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-secondary small fw-bold text-uppercase" style="letter-spacing: 0.5px;">
                                <th class="ps-4 py-3">Producto / Descripción</th>
                                <th class="text-center py-3" style="width: 120px;">Cantidad</th>
                                <th class="text-end py-3" style="width: 140px;">Precio Unit.</th>
                                <th class="text-end pe-4 py-3" style="width: 160px;">Subtotal</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($detalles as $detalle)
                                @php
                                    $precioUnit = (float) ($detalle->producto->precio_venta ?? 0);
                                    $subtotalItem = $detalle->cantidad * $precioUnit;
                                @endphp
                                <tr>
                                    {{-- Producto --}}
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-3 d-flex align-items-center justify-content-center border flex-shrink-0"
                                                 style="width: 42px; height: 42px; background: #f8fafc;">
                                                <i class="fa-solid fa-box text-warning fs-5"></i>
                                            </div>
                                            <div>
                                                <span class="fw-bold text-dark d-block" style="font-size: 0.95rem;">
                                                    {{ $detalle->producto->producto ?? 'Producto #' . $detalle->id_producto }}
                                                </span>
                                                <div class="d-flex align-items-center gap-2 mt-1">
                                                    <span class="badge bg-light text-muted border font-monospace small px-2 py-0">
                                                        ID: #{{ $detalle->id_producto }}
                                                    </span>
                                                    @if ($detalle->producto?->codigo_barras)
                                                        <span class="text-muted font-monospace small">
                                                            <i class="fa-solid fa-barcode me-1"></i>{{ $detalle->producto->codigo_barras }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Cantidad --}}
                                    <td class="text-center py-3">
                                        <span class="badge rounded-pill bg-light text-dark border px-3 py-2 fw-bold" style="font-size: 0.9rem;">
                                            {{ $detalle->cantidad }}
                                        </span>
                                    </td>

                                    {{-- Precio Unitario --}}
                                    <td class="text-end py-3 text-secondary fw-medium" style="font-size: 0.92rem;">
                                        ${{ number_format($precioUnit, 2, ',', '.') }}
                                    </td>

                                    {{-- Subtotal Ítem --}}
                                    <td class="text-end pe-4 py-3 fw-bold text-dark" style="font-size: 0.98rem;">
                                        ${{ number_format($subtotalItem, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="py-4">
                                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                                 style="width: 64px; height: 64px; background: rgba(245, 158, 11, 0.1);">
                                                <i class="fa-solid fa-basket-shopping text-warning fs-2"></i>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-1">El carrito de venta está vacío</h6>
                                            <p class="text-muted small mb-0">Selecciona un producto y cantidad arriba para comenzar la factura.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PIE DE LA TABLA CON CONTADORES --}}
                <div class="card-footer bg-light py-3 px-4 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <span class="text-muted small">
                        <i class="fa-solid fa-layer-group me-1 text-warning"></i>
                        Líneas en factura: <strong class="text-dark">{{ $detalles->count() }}</strong>
                    </span>
                    <span class="text-muted small">
                        <i class="fa-solid fa-cubes me-1 text-primary"></i>
                        Unidades totales: <strong class="text-dark">{{ $totalArticulos }}</strong>
                    </span>
                </div>
            </div>

        </div>

        {{-- COLUMNA DERECHA: RECEPTOR + RESUMEN + BOTÓN DE FACTURAR (STICKY) --}}
        <div class="col-12 col-lg-4">

            <div class="card border-0 shadow-sm rounded-4 bg-white sticky-top" style="top: 1.5rem; z-index: 10;">

                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-3 p-2 text-warning d-flex align-items-center justify-content-center" style="background: rgba(245, 158, 11, 0.12); width: 36px; height: 36px;">
                                <i class="fa-solid fa-file-invoice-dollar fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Detalle de Cobro</h6>
                                <small class="text-muted">Receptor y liquidación total</small>
                            </div>
                        </div>
                        <span class="badge rounded-pill bg-light text-secondary border px-2 py-1 small">
                            <i class="fa-solid fa-calendar-day me-1"></i> {{ date('d/m/Y') }}
                        </span>
                    </div>
                </div>

                <div class="card-body p-4">

                    <form action="#" method="POST" class="m-0">
                        @csrf

                        {{-- Selector de Cliente --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small mb-1">
                                Cliente Receptor <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="fa-solid fa-user text-warning"></i>
                                </span>
                                <select class="form-select bg-light border-start-0 py-2" name="id_cliente" required>
                                    <option value="">Seleccionar cliente...</option>
                                    @foreach ($cliente as $item)
                                        <option value="{{ $item->id_cliente }}" @selected(old('id_cliente') == $item->id_cliente)>
                                            {{ $item->nombre_razon_social }} ({{ $item->identificacion }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Fecha de Emisión --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-secondary small mb-1">
                                Fecha de Emisión <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="fa-solid fa-calendar"></i>
                                </span>
                                <input
                                    type="date"
                                    class="form-control bg-light border-start-0 py-2"
                                    name="fecha_emision"
                                    value="{{ old('fecha_emision', date('Y-m-d')) }}"
                                    required
                                >
                            </div>
                        </div>

                        {{-- Desglose Financiero --}}
                        <div class="p-3 rounded-3 bg-light border mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2 text-secondary small">
                                <span>Subtotal neto:</span>
                                <span class="fw-semibold text-dark fs-6">${{ number_format($subtotalCalculado, 2, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center text-secondary small">
                                <span>IVA / Impuestos:</span>
                                <span class="fw-semibold text-dark fs-6">$0,00</span>
                            </div>
                        </div>

                        {{-- DISPLAY OSCURO EJECUTIVO CON TOTAL A PAGAR EN ÁMBAR --}}
                        <div class="rounded-4 p-4 text-center mb-4 text-white shadow-sm" style="background: linear-gradient(135deg, #0b1120 0%, #171033 100%); border: 1px solid rgba(245, 158, 11, 0.25);">
                            <span class="text-uppercase fw-bold small text-warning d-block" style="letter-spacing: 1.5px; font-size: 0.78rem;">
                                Total a Pagar
                            </span>
                            <div class="fw-bolder mt-2" style="font-size: 2.3rem; color: #f59e0b; letter-spacing: -0.5px; line-height: 1;">
                                ${{ number_format($subtotalCalculado, 2, ',', '.') }}
                            </div>
                            <small class="text-white-50 mt-1 d-block" style="font-size: 0.78rem;">Moneda: COP</small>
                        </div>

                        {{-- BOTÓN COBRAR / GENERAR FACTURA --}}
                        <button type="submit"
                                class="btn btn-primary-gradient w-100 py-3 d-flex align-items-center justify-content-center gap-2 fw-bold text-uppercase shadow-sm mb-2"
                                style="font-size: 1rem; letter-spacing: 0.5px;"
                                @disabled($detalles->isEmpty())>
                            <i class="fa-solid fa-circle-check fs-5"></i>
                            <span>Generar Factura</span>
                        </button>

                        <a href="{{ route('facturas.index') }}" class="btn btn-link text-muted text-decoration-none w-100 text-center py-2 small">
                            <i class="fa-solid fa-arrow-left me-1"></i> Cancelar y volver al listado
                        </a>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>
@endsection
