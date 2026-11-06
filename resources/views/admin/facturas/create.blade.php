@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Punto de Venta POS - FactuWeb PRO')

@section('content')
@php
    $detalles = $borrador->detalles ?? collect();
    $subtotalCalculado = $detalles->sum(function ($det) {
        return $det->cantidad * ($det->producto?->precio_venta ?? 0);
    });
    $totalArticulos = $detalles->sum('cantidad');
@endphp

<div class="container-fluid p-0 pos-screen-wrapper">

    {{-- BARRA SUPERIOR COMPACTA DE LA CAJA (CABECERA EN 1 FILA) --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-1 pos-header-bar flex-shrink-0">
        <div class="d-flex align-items-center gap-2">
            <span class="badge rounded-pill px-3 py-1 text-white shadow-sm" style="background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); font-size: 0.72rem; letter-spacing: 0.5px;">
                <i class="fa-solid fa-cash-register me-1"></i> CAJA POS
            </span>
            <h5 class="fw-bold text-dark mb-0">
                Punto de Venta
            </h5>
            <span class="badge rounded-pill bg-light text-secondary border px-2 py-1 small d-none d-sm-inline">
                <i class="fa-solid fa-user-tie me-1 text-warning"></i> Cajero: {{ auth()->user()->name }}
            </span>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if (session('success'))
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small d-flex align-items-center gap-1">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </span>
            @endif
            <a href="{{ route('facturas.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 px-2 py-1 rounded-3">
                <i class="fa-solid fa-arrow-left"></i>
                <span class="d-none d-sm-inline">Historial</span>
            </a>
        </div>
    </div>

    {{-- CONTENIDO PRINCIPAL: 2 COLUMNAS SIN SCROLL DE PÁGINA --}}
    <div class="row g-3 pos-main-row flex-grow-1">

        {{-- COLUMNA IZQUIERDA: INGRESO RÁPIDO + TABLA DE PRODUCTOS (7 u 8 COLS) --}}
        <div class="col-12 col-lg-8 pos-col-left d-flex flex-column h-100">

            <div class="card border-0 shadow-sm rounded-4 h-100 d-flex flex-column overflow-hidden bg-white pos-left-card">
                
                {{-- 1. BARRA DE INGRESO RÁPIDO DE PRODUCTO --}}
                <div class="p-3 bg-light border-bottom flex-shrink-0 pos-product-input-bar">
                    <form action="{{ route('facturas.agregarProducto') }}" method="POST" class="m-0">
                        @csrf
                        <div class="row g-2 align-items-center">

                            {{-- Selector de Producto con Icono --}}
                            <div class="col">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white border-end-0 text-muted">
                                        <i class="fa-solid fa-barcode text-warning"></i>
                                    </span>
                                    <select name="id_producto" class="form-select bg-white border-start-0 fw-medium" required autofocus>
                                        <option value="">Buscar o seleccionar producto a facturar...</option>
                                        @foreach ($producto as $produc)
                                            <option value="{{ $produc->id }}" @selected(old('id_producto') == $produc->id)>
                                                {{ $produc->producto }} — ${{ number_format($produc->precio_venta, 2, ',', '.') }} (Stock: {{ $produc->stock }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Cantidad con Botones Rápidos o Input --}}
                            <div class="col-auto" style="width: 110px;">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white px-2 text-muted fw-semibold">Cant.</span>
                                    <input
                                        type="number"
                                        name="cantidad"
                                        class="form-control text-center fw-bold bg-white"
                                        min="1"
                                        value="{{ old('cantidad', 1) }}"
                                        required
                                    >
                                </div>
                            </div>

                            {{-- Botón Agregar --}}
                            <div class="col-auto">
                                <button type="submit" class="btn btn-sm btn-primary-gradient px-3 py-1 fw-bold d-inline-flex align-items-center gap-1 shadow-sm">
                                    <i class="fa-solid fa-cart-plus"></i>
                                    <span>Agregar</span>
                                </button>
                            </div>

                        </div>
                    </form>

                    {{-- ALERTA DE ERRORES DE STOCK / PRODUCTO --}}
                    @if ($errors->has('cantidad'))
                        <div class="alert alert-danger py-1 px-2 mt-2 mb-0 rounded-2 d-flex align-items-center gap-1 small border-0 shadow-sm">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <div>{{ $errors->first('cantidad') }}</div>
                        </div>
                    @endif

                    @if ($errors->has('id_producto'))
                        <div class="alert alert-danger py-1 px-2 mt-2 mb-0 rounded-2 d-flex align-items-center gap-1 small border-0 shadow-sm">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <div>{{ $errors->first('id_producto') }}</div>
                        </div>
                    @endif
                </div>

                {{-- 2. TABLA DEL CARRITO POS (SCROLL INTERNO AUTOCONTENIDO) --}}
                <div class="flex-grow-1 overflow-auto bg-white pos-table-container">
                    <table class="table table-hover table-sm align-middle mb-0">
                        <thead class="table-light sticky-top" style="z-index: 2;">
                            <tr class="text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                <th class="ps-3 py-2">Producto</th>
                                <th class="text-center py-2" style="width: 100px;">Cantidad</th>
                                <th class="text-end py-2" style="width: 130px;">Precio Unit.</th>
                                <th class="text-end pe-3 py-2" style="width: 140px;">Subtotal</th>
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
                                    <td class="ps-3 py-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-2 d-flex align-items-center justify-content-center text-secondary border flex-shrink-0"
                                                 style="width: 32px; height: 32px; background-color: #f8fafc;">
                                                <i class="fa-solid fa-box text-warning" style="font-size: 0.85rem;"></i>
                                            </div>
                                            <div class="text-truncate" style="max-width: 320px;">
                                                <span class="fw-bold text-dark d-block text-truncate" style="font-size: 0.88rem;">
                                                    {{ $detalle->producto->producto ?? 'Producto #' . $detalle->id_producto }}
                                                </span>
                                                <small class="text-muted font-monospace" style="font-size: 0.7rem;">
                                                    Cód / ID: #{{ $detalle->id_producto }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Cantidad --}}
                                    <td class="text-center py-2">
                                        <span class="badge rounded-pill bg-light text-dark border px-2 py-1 fw-bold" style="font-size: 0.82rem;">
                                            {{ $detalle->cantidad }}
                                        </span>
                                    </td>

                                    {{-- Precio Unitario --}}
                                    <td class="text-end py-2 text-secondary" style="font-size: 0.85rem;">
                                        ${{ number_format($precioUnit, 2, ',', '.') }}
                                    </td>

                                    {{-- Subtotal Ítem --}}
                                    <td class="text-end pe-3 py-2 fw-bold text-dark" style="font-size: 0.88rem;">
                                        ${{ number_format($subtotalItem, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <div class="py-4">
                                            <i class="fa-solid fa-cart-shopping fs-2 mb-2 text-warning opacity-50 d-block"></i>
                                            <span class="fw-bold text-dark d-block">El carrito está vacío</span>
                                            <small class="text-muted">Selecciona un producto arriba y pulsa Agregar para iniciar la factura.</small>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- 3. PIE DE LA TABLA (CONTADOR) --}}
                <div class="px-3 py-2 bg-light border-top d-flex justify-content-between align-items-center small text-muted flex-shrink-0 pos-table-footer">
                    <span>
                        <i class="fa-solid fa-list-check me-1 text-warning"></i>
                        <strong>{{ $detalles->count() }}</strong> {{ $detalles->count() === 1 ? 'producto' : 'productos' }} en lista
                    </span>
                    <span>
                        <i class="fa-solid fa-cubes me-1 text-primary"></i>
                        <strong>{{ $totalArticulos }}</strong> unidades totales
                    </span>
                </div>

            </div>

        </div>

        {{-- COLUMNA DERECHA: RECEPTOR + RESUMEN + BOTÓN DE COBRO (4 COLS) --}}
        <div class="col-12 col-lg-4 pos-col-right d-flex flex-column h-100">

            <form action="#" method="POST" class="m-0 h-100 d-flex flex-column">
                @csrf

                <div class="card border-0 shadow-sm rounded-4 h-100 d-flex flex-column justify-content-between p-3 bg-white overflow-hidden pos-right-card">

                    {{-- PARTE SUPERIOR: DATOS DEL CLIENTE Y FECHA --}}
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <span class="fw-bold small text-uppercase text-secondary" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                                <i class="fa-solid fa-user-tag text-warning me-1"></i> Datos del Comprador
                            </span>
                            <span class="badge rounded-pill bg-light text-secondary border px-2 py-1" style="font-size: 0.7rem;">
                                <i class="fa-solid fa-calendar-day me-1"></i> {{ date('d/m/Y') }}
                            </span>
                        </div>

                        {{-- Selector de Cliente --}}
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.78rem;">
                                Cliente <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="fa-solid fa-user"></i>
                                </span>
                                <select class="form-select bg-light border-start-0" name="id_cliente" required>
                                    <option value="">Seleccionar cliente...</option>
                                    @foreach ($cliente as $item)
                                        <option value="{{ $item->id_cliente }}" @selected(old('id_cliente') == $item->id_cliente)>
                                            {{ $item->nombre_razon_social }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Fecha de Emisión --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.78rem;">
                                Fecha de Emisión
                            </label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="fa-solid fa-calendar"></i>
                                </span>
                                <input
                                    type="date"
                                    class="form-control bg-light border-start-0"
                                    name="fecha_emision"
                                    value="{{ old('fecha_emision', date('Y-m-d')) }}"
                                    required
                                >
                            </div>
                        </div>

                        {{-- Desglose Subtotal e Impuestos --}}
                        <div class="p-2 rounded-3 bg-light border mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1 text-secondary" style="font-size: 0.82rem;">
                                <span>Subtotal:</span>
                                <span class="fw-semibold text-dark">${{ number_format($subtotalCalculado, 2, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center text-secondary" style="font-size: 0.82rem;">
                                <span>IVA / Impuestos:</span>
                                <span class="fw-semibold text-dark">$0,00</span>
                            </div>
                        </div>
                    </div>

                    {{-- PARTE INFERIOR: TOTAL GRANDE Y BOTÓN DE EMISIÓN POS --}}
                    <div>
                        {{-- DISPLAY OSCURO EJECUTIVO CON TOTAL A PAGAR EN ÁMBAR --}}
                        <div class="rounded-3 p-3 text-center mb-2 text-white" style="background: linear-gradient(135deg, #0b1120 0%, #171033 100%); border: 1px solid rgba(245, 158, 11, 0.25);">
                            <span class="text-uppercase small fw-bold text-warning d-block" style="letter-spacing: 1.5px; font-size: 0.72rem;">
                                Total a Pagar
                            </span>
                            <div class="fw-bolder mt-1" style="font-size: 2rem; color: #f59e0b; letter-spacing: -0.5px; line-height: 1;">
                                ${{ number_format($subtotalCalculado, 2, ',', '.') }}
                            </div>
                        </div>

                        {{-- BOTÓN COBRAR / GENERAR FACTURA --}}
                        <button type="submit"
                                class="btn btn-primary-gradient w-100 py-3 d-flex align-items-center justify-content-center gap-2 fw-bold text-uppercase shadow-sm mb-1"
                                style="font-size: 0.95rem; letter-spacing: 0.5px;"
                                @disabled($detalles->isEmpty())>
                            <i class="fa-solid fa-circle-check fs-5"></i>
                            <span>Generar Factura</span>
                        </button>

                        <a href="{{ route('facturas.index') }}" class="btn btn-sm btn-link text-muted text-decoration-none w-100 text-center py-1 small">
                            <i class="fa-solid fa-xmark me-1"></i> Cancelar Venta
                        </a>
                    </div>

                </div>

            </form>

        </div>

    </div>

</div>

{{-- ESTILOS ULTRA-COMPACTOS PARA AJUSTE PERFECTO A LA PANTALLA (SIN SCROLL DE VENTANA) --}}
@section('css')
<style>
    @media (min-width: 992px) {
        /* Optimización de viewport para pantalla POS: cero scroll externo */
        .app-content {
            padding: 0.75rem 1.25rem !important;
            overflow: hidden !important;
        }

        .pos-screen-wrapper {
            height: calc(100vh - 80px - 1.5rem);
            max-height: calc(100vh - 80px - 1.5rem);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .pos-header-bar {
            flex-shrink: 0;
        }

        .pos-main-row {
            flex: 1 1 0;
            min-height: 0;
            overflow: hidden;
        }

        .pos-col-left, .pos-col-right {
            height: 100%;
            min-height: 0;
        }

        .pos-left-card {
            height: 100%;
            min-height: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .pos-product-input-bar {
            flex-shrink: 0;
        }

        .pos-table-container {
            flex: 1 1 0;
            min-height: 0;
            overflow-y: auto !important;
        }

        .pos-table-footer {
            flex-shrink: 0;
        }

        .pos-right-card {
            height: 100%;
            min-height: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow-y: auto;
        }
    }
</style>
@endsection
@endsection
