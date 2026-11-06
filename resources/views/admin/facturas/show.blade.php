@extends('layouts.sidebaradmin')

@section('tituloPagina', "Factura {$factura->prefijo_resolucion}-{$factura->consecutivo} - FactuWeb PRO")

@section('content')
<div class="container-fluid px-0">

    {{-- BARRA SUPERIOR DE ACCIONES (OCULTA AL IMPRIMIR) --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 print-hide">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-1 small fw-semibold">
                    <i class="fa-solid fa-circle-check me-1"></i> Factura Emitida
                </span>
                <span class="badge rounded-pill bg-white text-secondary border px-3 py-1 shadow-sm small">
                    <i class="fa-solid fa-file-invoice text-warning me-1"></i> {{ $factura->prefijo_resolucion }}-{{ $factura->consecutivo }}
                </span>
            </div>
            <h3 class="fw-bold text-dark mb-0">Comprobante de Venta Electrónica</h3>
            <p class="text-muted small mb-0">Documento fiscal generado conforme a los estándares de Facturación Electrónica DIAN.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('facturas.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3 shadow-sm">
                <i class="fa-solid fa-arrow-left"></i>
                <span class="d-none d-sm-inline">Historial</span>
            </a>

            <button type="button" onclick="window.print()" class="btn btn-outline-dark d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3 shadow-sm">
                <i class="fa-solid fa-print"></i>
                <span>Imprimir Factura</span>
            </button>

            <a href="{{ route('facturas.create') }}" class="btn btn-primary-gradient d-inline-flex align-items-center gap-2 px-4 py-2 rounded-3 shadow-sm fw-bold">
                <i class="fa-solid fa-cart-plus"></i>
                <span>Nueva Venta (POS)</span>
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success py-3 px-4 mb-4 rounded-4 border-0 shadow-sm d-flex align-items-center gap-3 print-hide">
            <i class="fa-solid fa-circle-check fs-4 text-success"></i>
            <div>
                <strong class="d-block">¡Transacción Exitosa!</strong>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- DOCUMENTO FACTURA / COMPROBANTE FISCAL --}}
    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white invoice-sheet mx-auto" style="max-width: 900px;">

        {{-- 1. CABECERA: EMPRESA Y FOLIO FACTURA --}}
        <div class="row align-items-start pb-4 border-bottom g-3">
            <div class="col-12 col-md-7">
                <div class="d-flex align-items-center gap-3 mb-2">
                    @if ($factura->empresa?->logo_url)
                        <img src="{{ asset($factura->empresa->logo_url) }}" alt="Logo" class="rounded-3 border p-1" style="max-height: 60px; max-width: 120px; object-fit: contain;">
                    @else
                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white fw-bold fs-4" style="width: 54px; height: 54px; background: linear-gradient(135deg, #12102f, #ea580c);">
                            <i class="fa-solid fa-building"></i>
                        </div>
                    @endif
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $factura->empresa?->razon_social ?? 'Empresa Comercial' }}</h4>
                        <span class="text-muted small fw-semibold">NIT: {{ $factura->empresa?->nit ?? 'No registrado' }}</span>
                    </div>
                </div>
                <div class="text-secondary small lh-sm">
                    <div><i class="fa-solid fa-location-dot text-warning me-1"></i> {{ $factura->empresa?->direccion ?? 'Dirección no registrada' }}</div>
                    <div class="mt-1"><i class="fa-solid fa-phone text-warning me-1"></i> {{ $factura->empresa?->telefono ?? 'Sin teléfono' }} | <i class="fa-solid fa-envelope text-warning me-1"></i> {{ $factura->empresa?->email ?? 'correo@empresa.com' }}</div>
                </div>
            </div>

            <div class="col-12 col-md-5 text-md-end">
                <span class="badge rounded-pill px-3 py-1 text-white shadow-sm mb-2" style="background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); font-size: 0.78rem; letter-spacing: 0.5px;">
                    FACTURA ELECTRÓNICA DE VENTA
                </span>
                <h3 class="fw-bold text-dark mb-1 font-monospace">{{ $factura->prefijo_resolucion }}-{{ $factura->consecutivo }}</h3>
                <div class="text-secondary small">
                    <div><strong>Fecha Emisión:</strong> {{ \Carbon\Carbon::parse($factura->fecha_emision)->format('d/m/Y h:i A') }}</div>
                    <div>
                        <strong>Estado DIAN:</strong>
                        @if ($factura->estado_dian === 'aceptada')
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0">Aceptada</span>
                        @else
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-0">{{ ucfirst($factura->estado_dian) }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. RESOLUCIÓN DIAN INFORMATIVA --}}
        @if ($factura->resolucion)
            <div class="py-2 px-3 my-3 rounded-3 bg-light border text-muted small text-center">
                <i class="fa-solid fa-scale-balanced me-1 text-warning"></i>
                <strong>Resolución DIAN N° {{ $factura->resolucion->numero_resolucion }}</strong> |
                Prefijo <strong>{{ $factura->resolucion->prefijo }}</strong> del {{ number_format($factura->resolucion->rango_inicial, 0) }} al {{ number_format($factura->resolucion->rango_final, 0) }} |
                Vigencia hasta: {{ \Carbon\Carbon::parse($factura->resolucion->fecha_vigencia)->format('d/m/Y') }}
            </div>
        @endif

        {{-- 3. DATOS DEL CLIENTE / RECEPTOR Y CAJERO --}}
        <div class="row g-3 py-3 border-bottom">
            <div class="col-12 col-md-7">
                <span class="text-uppercase small fw-bold text-warning d-block mb-1" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                    <i class="fa-solid fa-user-tag me-1"></i> Datos del Adquirente / Cliente
                </span>
                <h5 class="fw-bold text-dark mb-1">{{ $factura->cliente?->nombre_razon_social ?? 'Consumidor Final' }}</h5>
                <div class="text-secondary small">
                    <div><strong>Identificación / NIT:</strong> {{ $factura->cliente?->identificacion ?? '222222222222' }}</div>
                    <div><strong>Dirección:</strong> {{ $factura->cliente?->direccion ?? 'Mostrador' }}</div>
                    <div><strong>Teléfono:</strong> {{ $factura->cliente?->telefono ?? 'N/A' }} | <strong>Email:</strong> {{ $factura->cliente?->email ?? 'N/A' }}</div>
                </div>
            </div>

            <div class="col-12 col-md-5">
                <span class="text-uppercase small fw-bold text-secondary d-block mb-1" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                    <i class="fa-solid fa-circle-info me-1"></i> Datos de Operación
                </span>
                <div class="text-secondary small">
                    <div><strong>Cajero / Emisor:</strong> {{ $factura->usuario?->name ?? 'Caja Principal' }}</div>
                    <div><strong>Tipo de Venta:</strong> Contado / Punto de Venta (POS)</div>
                    <div><strong>Moneda:</strong> COP (Pesos Colombianos)</div>
                    @if ($factura->fecha_validacion_dian)
                        <div><strong>Validación DIAN:</strong> {{ \Carbon\Carbon::parse($factura->fecha_validacion_dian)->format('d/m/Y h:i:s A') }}</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- 4. TABLA DE PRODUCTOS FACTURADOS --}}
        <div class="table-responsive my-4">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-secondary small fw-bold text-uppercase" style="letter-spacing: 0.5px;">
                        <th class="ps-3 py-2" style="width: 40px;">#</th>
                        <th class="py-2">Descripción</th>
                        <th class="text-center py-2" style="width: 100px;">Cant.</th>
                        <th class="text-end py-2" style="width: 130px;">Precio Unit.</th>
                        <th class="text-center py-2" style="width: 90px;">IVA</th>
                        <th class="text-end pe-3 py-2" style="width: 140px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($factura->detalles as $index => $det)
                        <tr>
                            <td class="ps-3 py-3 text-muted small">{{ $index + 1 }}</td>
                            <td class="py-3">
                                <span class="fw-bold text-dark d-block" style="font-size: 0.95rem;">
                                    {{ $det->producto?->producto ?? 'Producto #' . $det->id_producto }}
                                </span>
                                @if ($det->producto?->codigo_barras)
                                    <small class="text-muted font-monospace"><i class="fa-solid fa-barcode me-1"></i>{{ $det->producto->codigo_barras }}</small>
                                @endif
                            </td>
                            <td class="text-center py-3 fw-bold">{{ $det->cantidad }}</td>
                            <td class="text-end py-3 text-secondary">${{ number_format($det->precio_unitario_venta, 2, ',', '.') }}</td>
                            <td class="text-center py-3">
                                <span class="badge bg-light text-muted border small">
                                    {{ number_format((float) ($det->producto?->iva ?? 0), 0) }}%
                                </span>
                            </td>
                            <td class="text-end pe-3 py-3 fw-bold text-dark">${{ number_format($det->subtotal_linea, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No se registraron líneas de detalle en esta factura.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- 5. RESUMEN DE TOTALES --}}
        <div class="row justify-content-end mb-4">
            <div class="col-12 col-md-5">
                <div class="p-3 rounded-3 bg-light border">
                    <div class="d-flex justify-content-between align-items-center mb-2 small text-secondary">
                        <span>Subtotal Neto:</span>
                        <span class="fw-bold text-dark fs-6">${{ number_format($factura->subtotal, 2, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2 small text-secondary">
                        <span>Total IVA / Impuestos:</span>
                        <span class="fw-bold text-dark fs-6">${{ number_format($factura->total_iva, 2, ',', '.') }}</span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between align-items-center text-dark">
                        <span class="fw-bold fs-6">Total a Pagar:</span>
                        <span class="fw-bolder fs-4" style="color: #ea580c;">${{ number_format($factura->total_pagar, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 6. SECCIÓN TÉCNICA DIAN: CUFE Y CÓDIGO QR --}}
        <div class="p-3 rounded-4 border bg-white mt-2">
            <div class="row align-items-center g-3">
                <div class="col-auto text-center">
                    @if ($factura->cufe)
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($factura->codigo_qr ?? $factura->cufe) }}"
                             alt="QR DIAN"
                             class="border rounded p-1 shadow-sm"
                             style="width: 100px; height: 100px;">
                    @else
                        <div class="border rounded d-flex align-items-center justify-content-center text-muted" style="width: 100px; height: 100px; background: #f8fafc;">
                            <i class="fa-solid fa-qrcode fs-2"></i>
                        </div>
                    @endif
                </div>
                <div class="col">
                    <span class="text-uppercase small fw-bold text-secondary d-block" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                        <i class="fa-solid fa-shield-halved text-success me-1"></i> Firma Digital DIAN (CUFE)
                    </span>
                    <div class="font-monospace text-muted mt-1 p-2 rounded bg-light border text-break small" style="font-size: 0.72rem; line-height: 1.3;">
                        {{ $factura->cufe ?? 'Factura en trámite de validación' }}
                    </div>
                    <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">
                        Representación gráfica de factura electrónica de venta generada por software FactuWeb PRO.
                    </small>
                </div>
            </div>
        </div>

    </div>

</div>

{{-- ESTILOS EXCLUSIVOS PARA IMPRESIÓN LIMPIA DE LA FACTURA --}}
@section('css')
<style>
    @media print {
        /* Ocultar elementos de navegación y botones */
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

        .invoice-sheet {
            box-shadow: none !important;
            border: none !important;
            max-width: 100% !important;
            padding: 0 !important;
            width: 100% !important;
        }

        .container-fluid {
            padding: 0 !important;
        }
    }
</style>
@endsection
@endsection

