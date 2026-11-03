 
@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Nueva factura - FactuWeb PRO')

@section('content')
<div class="container-fluid">

    {{-- ENCABEZADO --}}
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
                            <tr>
                                <td>
                                    <select class="form-select" name="productos[]">
                                        <option value="">Seleccionar producto</option>
                                    </select>
                                </td>

                                <td>
                                    <input
                                        type="number"
                                        class="form-control"
                                        name="cantidades[]"
                                        min="1"
                                        placeholder="Cantidad">
                                </td>

                                <td class="text-muted">
                                    Se calculará automáticamente
                                </td>

                                <td class="text-muted">
                                    Se calculará automáticamente
                                </td>
                            </tr>
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