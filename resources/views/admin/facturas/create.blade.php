 
@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Nueva factura - FactuWeb PRO')

@section('content')
<div class="container-fluid">

    {{-- ENCABEZADO --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Nueva factura</h3>
            <p class="text-muted mb-0">
                Registra una nueva factura para tus clientes.
            </p>
        </div>

        <a href="{{ route('facturas.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Volver
        </a>
    </div>

    <form action="#" method="POST">
        @csrf

        {{-- DATOS GENERALES --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-semibold mb-0">
                    <i class="fas fa-file-invoice me-2"></i>
                    Datos generales
                </h5>
            </div>

            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fecha de emisión</label>
                        <input type="date" class="form-control" name="fecha_emision">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Cliente</label>

                        <select class="form-select" name="id_cliente">
                            <option value="">Seleccionar cliente</option>

                            @foreach ($cliente as $item)
                                <option
                                    value="{{ $item->id_cliente }}"
                                    @selected(old('id_cliente') == $item->id_cliente)>
                                    {{ $item->nombre_razon_social }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                </div>
            </div>
        </div>

        {{-- PRODUCTOS --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-semibold mb-0">
                    <i class="fas fa-boxes me-2"></i>
                    Productos de la factura
                </h5>
            </div>

            <div class="card-body">
                {{-- FORMULARIO PARA AGREGAR PRODUCTOS --}}
                
                <form action="{{ route('facturas.agregarProducto') }}" method="POST" class="mb-4">
                    @csrf

                    <div class="row g-3 align-items-end">

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Seleccionar producto</label>

                            <select name="id_producto" class="form-select" required>
                                <option value="">Seleccionar producto</option>

                                @foreach ($producto as $produc)
                                    <option value="{{ $produc->id_producto }}">
                                        {{ $produc->producto }}
                                        - ${{ number_format($produc->precio_venta, 2, ',', '.') }}
                                        (Stock: {{ $produc->stock }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Cantidad</label>

                            <input
                                type="number"
                                name="cantidad"
                                class="form-control"
                                min="1"
                                value="1"
                                required
                            >
                        </div>

                        <div class="col-md-3">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-plus me-2"></i>
                                Agregar producto
                            </button>
                        </div>

                    </div>
                </form>

                @if ($errors->has('cantidad'))
                    <div class="alert alert-danger py-2">
                        {{ $errors->first('cantidad') }}
                    </div>
                @endif

                @if ($errors->has('id_producto'))
                    <div class="alert alert-danger py-2">
                        {{ $errors->first('id_producto') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="min-width: 250px;">Producto</th>
                                <th style="width: 150px;">Cantidad</th>
                                <th style="width: 180px;">Precio unitario</th>
                                <th style="width: 180px;">Subtotal</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($borrador->detalles as $detalle)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">
                                            {{ $detalle->producto->producto }}
                                        </div>
                                    </td>

                                    <td>
                                        {{ $detalle->cantidad }}
                                    </td>

                                    <td>
                                        ${{ number_format($detalle->producto->precio_venta, 2, ',', '.') }}
                                    </td>

                                    <td>
                                        ${{ number_format(
                                            $detalle->cantidad * $detalle->producto->precio_venta,
                                            2,
                                            ',',
                                            '.'
                                        ) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="fas fa-shopping-cart me-2"></i>
                                        No hay productos agregados al borrador.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <p class="text-muted small mb-0">
                    La selección de productos y el cálculo de valores se conectarán posteriormente.
                </p>

            </div>
        </div>

        {{-- RESUMEN --}}
        <div class="row justify-content-end mb-4">
            <div class="col-md-5 col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">

                        <h5 class="fw-bold mb-3">Resumen de factura</h5>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span>$0,00</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">IVA</span>
                            <span>$0,00</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Total a pagar</span>
                            <span class="fw-bold text-primary fs-5">$0,00</span>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- ACCIONES --}}
        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('facturas.index') }}" class="btn btn-light border">
                Cancelar
            </a>

            <button type="submit" class="btn btn-primary" disabled>
                <i class="fas fa-file-invoice me-2"></i>
                Generar factura
            </button>
        </div>

    </form>

</div>
@endsection