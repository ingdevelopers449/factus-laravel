@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Facturas - FactuWeb PRO')

@section('content')
<div class="container-fluid">

    {{-- ENCABEZADO --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Gestión de Facturas</h3>
            <p class="text-muted mb-0">
                Consulta y administra las facturas generadas.
            </p>
        </div>

        <a href="#" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            Nueva factura
        </a>
    </div>

    {{-- TARJETA DEL LISTADO --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="fas fa-file-invoice me-2"></i>
                Listado de facturas
            </h5>
        </div>

        <div class="card-body">
            {{-- BUSCADOR DE FACTURAS --}}
            <form action="{{ route('facturas.index') }}" method="GET" class="mb-4">
                @csrf
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-search"></i>
                            </span>

                            <input
                                type="text"
                                name="buscar"
                                class="form-control"
                                placeholder="Buscar por número de factura..."
                                value="{{ $buscar ?? '' }}"
                            >

                            <button type="submit" class="btn btn-primary">
                                Buscar
                            </button>
                        </div>
                    </div>

                    <div class="col-md-auto">
                        <a href="{{ route('facturas.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>
                            Limpiar
                        </a>
                    </div>
                </div>
            </form>  {{-- AQUI TERMINA EL FORMULARIO --}}

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>Factura</th>
                            <th>Fecha de emisión</th>
                            <th>Cliente</th>
                            <th>Subtotal</th>
                            <th>IVA</th>
                            <th>Total</th>
                            <th>Estado DIAN</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($facturas as $factura)

                            <tr>
                                <td class="fw-semibold">
                                    {{ $factura->prefijo_resolucion }}-{{ $factura->consecutivo }}
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($factura->fecha_emision)->format('d/m/Y') }}
                                </td>

                                <td>
                                    {{ $factura->cliente?->nombre_razon_social ?? 'Cliente no disponible' }}
                                </td>

                                <td>
                                    ${{ number_format($factura->subtotal, 2, ',', '.') }}
                                </td>

                                <td>
                                    ${{ number_format($factura->total_iva, 2, ',', '.') }}
                                </td>

                                <td class="fw-bold">
                                    ${{ number_format($factura->total_pagar, 2, ',', '.') }}
                                </td>

                                <td>
                                    @if ($factura->estado_dian === 'aceptada')
                                        <span class="badge bg-success">Aceptada</span>
                                    @elseif ($factura->estado_dian === 'rechazada')
                                        <span class="badge bg-danger">Rechazada</span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            {{ ucfirst($factura->estado_dian) }}
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Ver factura"
                                        disabled>
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="8" class="text-center py-5">

                                    <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>

                                    <h5 class="text-muted">
                                        No hay facturas registradas
                                    </h5>

                                    <p class="text-muted mb-0">
                                        Cuando generes una factura, aparecerá en este listado.
                                    </p>

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- PAGINACIÓN --}}
            @if ($facturas->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $facturas->links('pagination::bootstrap-5') }}
                </div>
            @endif

        </div>
    </div>

</div>
@endsection