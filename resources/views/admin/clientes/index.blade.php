
@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Gestión de Clientes - FactuWeb PRO')

@section('content')
<div class="container-fluid p-0">

    {{-- Encabezado --}}
    <div class="d-flex flex-column flex-md-row
                justify-content-between align-items-md-center
                gap-3 mb-4">

        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-users text-primary"></i>
                Gestión de Clientes
            </h4>

            <p class="text-muted small mb-0">
                Administra los clientes registrados en FactuWeb PRO.
            </p>
        </div>

        <button type="button"
                class="btn btn-primary-gradient d-inline-flex
                       align-items-center gap-2 px-3 py-2 shadow-sm"
                data-bs-toggle="modal"
                data-bs-target="#modalNuevoCliente">

            <i class="fa-solid fa-user-plus"></i>
            <span>Nuevo Cliente</span>
        </button>
    </div>

    {{-- Tabla de clientes --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">

        <div class="card-header bg-white border-bottom py-3 px-4
                    d-flex justify-content-between align-items-center">

            <span class="fw-semibold text-secondary small">
                <i class="fa-solid fa-list-check me-1"></i>
                Clientes registrados
            </span>

            <span class="badge rounded-pill bg-light text-dark border px-3 py-2">
                Total: {{ $clientes->total() }}
            </span>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">

                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Identificación</th>
                        <th>Cliente / Razón social</th>
                        <th>Correo electrónico</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($clientes as $cliente)
                        <tr>
                            <td class="ps-4 py-3">
                                <span class="fw-semibold">
                                    {{ $cliente->identificacion }}
                                </span>
                            </td>

                            <td>
                                {{ $cliente->nombre_razon_social }}
                            </td>

                            <td>
                                <span class="text-secondary small">
                                    {{ $cliente->email ?? 'No registrado' }}
                                </span>
                            </td>

                            <td>
                                {{ $cliente->telefono ?? 'No registrado' }}
                            </td>

                            <td>
                                <span class="text-secondary small">
                                    {{ $cliente->direccion ?? 'No registrada' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fa-solid fa-users-slash fs-1
                                          d-block mb-3 text-secondary opacity-50"></i>

                                <p class="mb-1 fw-semibold">
                                    No hay clientes registrados.
                                </p>

                                <p class="text-muted small mb-0">
                                    Cuando registres un cliente,
                                    aparecerá en esta tabla.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        {{-- Paginación --}}
        @if ($clientes->hasPages())
            <div class="card-footer bg-white border-top py-3 px-4">
                <div class="d-flex justify-content-end">
                    {{ $clientes->links() }}
                </div>
            </div>
        @endif

    </div>
</div>
@endsection

