
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

        {{-- Cabecera con título/conteo a la izquierda y buscador integrado a la derecha --}}
        <div class="card-header bg-white border-bottom py-3 px-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

                {{-- Título y Badge --}}
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold text-secondary small">
                        <i class="fa-solid fa-list-check me-1"></i>
                        Clientes registrados
                    </span>

                    <span class="badge rounded-pill bg-light text-dark border px-3 py-1 small">
                        Total: {{ $clientes->total() }}
                    </span>
                </div>

                {{-- Buscador Integrado --}}
                <form action="{{ route('admin.clientes.index') }}" method="GET" class="m-0" style="min-width: 280px; max-width: 420px; width: 100%;">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text"
                               name="buscar"
                               class="form-control bg-light border-start-0 ps-0"
                               placeholder="Identificación, nombre o email..."
                               value="{{ $buscar }}">
                        <button type="submit" class="btn btn-primary-gradient px-3">
                            Buscar
                        </button>
                        @if ($buscar)
                            <a href="{{ route('admin.clientes.index') }}" class="btn btn-light border" title="Limpiar búsqueda">
                                <i class="fa-solid fa-xmark text-secondary"></i>
                            </a>
                        @endif
                    </div>
                </form>

            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover table-admin">

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


{{-- Modal: Nuevo Cliente --}}
<div class="modal fade" id="modalNuevoCliente"
     tabindex="-1" aria-labelledby="tituloModalCliente"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">

            {{-- Encabezado --}}
            <div class="modal-header px-4 py-3">
                <div>
                    <h5 class="modal-title fw-bold" id="tituloModalCliente">
                        <i class="fa-solid fa-user-plus text-primary me-2"></i>
                        Nuevo Cliente
                    </h5>
                    <p class="text-muted small mb-0 mt-1">
                        Registra los datos del cliente.
                    </p>
                </div>

                <button type="button" class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
            </div>

            {{-- Formulario --}}
            <form id="formNuevoCliente"
                  action="{{ route('admin.clientes.store') }}"
                  method="POST">
                @csrf

                <div class="modal-body p-4">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label for="identificacion"
                                   class="form-label fw-semibold">
                                Identificación <span class="text-danger">*</span>
                            </label>

                            <input type="text"
                                   class="form-control"
                                   id="identificacion"
                                   name="identificacion"
                                   maxlength="20"
                                   value="{{ old('identificacion') }}"
                                   placeholder="Número de documento"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label for="nombre_razon_social"
                                   class="form-label fw-semibold">
                                Nombre o razón social <span class="text-danger">*</span>
                            </label>

                            <input type="text"
                                   class="form-control"
                                   id="nombre_razon_social"
                                   name="nombre_razon_social"
                                   maxlength="150"
                                   value="{{ old('nombre_razon_social') }}"
                                   placeholder="Nombre completo o empresa"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">
                                Correo electrónico
                            </label>

                            <input type="email"
                                   class="form-control"
                                   id="email"
                                   name="email"
                                   maxlength="150"
                                   value="{{ old('email') }}"
                                   placeholder="cliente@correo.com">
                        </div>

                        <div class="col-md-6">
                            <label for="telefono" class="form-label fw-semibold">
                                Teléfono
                            </label>

                            <input type="text"
                                   class="form-control"
                                   id="telefono"
                                   name="telefono"
                                   maxlength="20"
                                   value="{{ old('telefono') }}"
                                   placeholder="Número de contacto">
                        </div>

                        <div class="col-12">
                            <label for="direccion" class="form-label fw-semibold">
                                Dirección
                            </label>

                            <input type="text"
                                   class="form-control"
                                   id="direccion"
                                   name="direccion"
                                   maxlength="255"
                                   value="{{ old('direccion') }}"
                                   placeholder="Dirección del cliente">
                        </div>

                    </div>

                    <p class="text-muted small mt-3 mb-0">
                        <span class="text-danger">*</span>
                        Campos obligatorios.
                    </p>
                </div>

                {{-- Botones --}}
                <div class="modal-footer px-4 py-3">
                    <button type="button"
                            class="btn btn-light border"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit"
                            class="btn btn-primary-gradient">
                        <i class="fa-solid fa-floppy-disk me-1"></i>
                        Guardar cliente
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection

