@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Gestión de Productos - FactuWeb PRO')

@section('content')

<div class="container-fluid p-0">

    {{-- Encabezado de la Sección --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-box-open text-primary"></i>
                Gestión de Productos
            </h4>
            <p class="text-muted small mb-0">
                Administra el catálogo de productos, inventario y precios en FactuWeb PRO.
            </p>
        </div>

        <button type="button"
                class="btn btn-primary-gradient d-inline-flex align-items-center gap-2 px-3 py-2 shadow-sm"
                data-bs-toggle="modal"
                data-bs-target="#modalCrearProducto">
            <i class="fa-solid fa-plus"></i>
            <span>Nuevo Producto</span>
        </button>
    </div>

    {{-- Alerta de éxito --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="fa-solid fa-circle-check fs-5 text-success"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    {{-- Alerta de errores de validación --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="fa-solid fa-triangle-exclamation text-danger fs-5"></i>
                <strong class="text-danger">Por favor corrige los siguientes errores:</strong>
            </div>
            <ul class="mb-0 ps-4 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    {{-- Tarjeta Principal con la Tabla --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">

        {{-- Cabecera con título/conteo a la izquierda y buscador integrado a la derecha --}}
        <div class="card-header bg-white border-bottom py-3 px-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

                {{-- Título y Badge --}}
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold text-secondary small">
                        <i class="fa-solid fa-list-check me-1"></i> Listado de Productos
                    </span>
                    <span class="badge rounded-pill bg-light text-dark border px-3 py-1 small">
                        Total: {{ $productos->total() }} registros
                    </span>
                </div>

                {{-- Buscador Integrado --}}
                <form action="{{ route('producto.index') }}" method="GET" class="m-0" style="min-width: 280px; max-width: 420px; width: 100%;">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="search"
                               name="buscar"
                               class="form-control bg-light border-start-0 ps-0"
                               placeholder="Buscar por nombre o código..."
                               value="{{ request('buscar') }}">
                        <button type="submit" class="btn btn-primary-gradient px-3">
                            Buscar
                        </button>
                        @if (request('buscar'))
                            <a href="{{ route('producto.index') }}" class="btn btn-light border" title="Limpiar búsqueda">
                                <i class="fa-solid fa-xmark text-secondary"></i>
                            </a>
                        @endif
                    </div>
                </form>

            </div>
        </div>

        {{-- Tabla de Productos --}}
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Producto</th>
                        <th>Código</th>
                        <th>Categoría</th>
                        <th>P. Compra</th>
                        <th>P. Venta</th>
                        <th>Stock</th>
                        <th>IVA</th>
                        <th>Estado</th>
                        <th class="text-center pe-4">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($productos as $producto)
                        <tr>
                            {{-- Imagen y nombre --}}
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3" style="min-width: 200px;">
                                    @if ($producto->imagen_url)
                                        <img src="{{ $producto->imagen_url }}"
                                             alt="{{ $producto->producto }}"
                                             width="44"
                                             height="44"
                                             class="rounded-3 border object-fit-cover shadow-sm flex-shrink-0"
                                             loading="lazy"
                                             onerror="this.onerror=null; this.src='https://placehold.co/44x44?text=Foto';">
                                    @else
                                        <div class="bg-light border rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 text-secondary"
                                             style="width: 44px; height: 44px;">
                                            <i class="fa-solid fa-box-open fs-5"></i>
                                        </div>
                                    @endif

                                    <div>
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 240px;">
                                            {{ $producto->producto }}
                                        </div>
                                        <small class="text-muted">ID: #{{ $producto->id }}</small>
                                    </div>
                                </div>
                            </td>

                            {{-- Código de barras --}}
                            <td>
                                @if ($producto->codigo_barras)
                                    <span class="badge rounded-pill bg-light text-secondary border px-2 py-1 small font-monospace">
                                        <i class="fa-solid fa-barcode me-1"></i>{{ $producto->codigo_barras }}
                                    </span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>

                            {{-- Categoría --}}
                            <td>
                                <span class="badge rounded-pill bg-light text-dark border px-3 py-1 small">
                                    <i class="fa-solid fa-tag me-1 text-primary"></i>
                                    {{ $producto->categoria?->categoria ?? 'Sin categoría' }}
                                </span>
                            </td>

                            {{-- Precio de compra --}}
                            <td class="text-nowrap small text-secondary">
                                ${{ number_format((float) $producto->precio_compra, 2, ',', '.') }}
                            </td>

                            {{-- Precio de venta --}}
                            <td class="text-nowrap fw-bold text-dark">
                                ${{ number_format((float) $producto->precio_venta, 2, ',', '.') }}
                            </td>

                            {{-- Stock con badges estilizados --}}
                            <td>
                                @if ($producto->stock <= 0)
                                    <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fw-semibold small">
                                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Agotado
                                    </span>
                                @elseif ($producto->stock <= 5)
                                    <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 fw-semibold small">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $producto->stock }} unids.
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-semibold small">
                                        <i class="fa-solid fa-circle-check me-1"></i> {{ $producto->stock }} unids.
                                    </span>
                                @endif
                            </td>

                            {{-- IVA --}}
                            <td>
                                <span class="badge rounded-pill bg-light text-muted border px-2 py-1 small">
                                    {{ number_format((float) $producto->iva, 0) }}%
                                </span>
                            </td>

                            {{-- Estado (Badge acorde al Sistema de Diseño) --}}
                            <td>
                                @if ($producto->state)
                                    <span class="badge-status-active">
                                        <i class="fa-solid fa-circle fa-2xs me-1" style="color: #16a34a;"></i> Activo
                                    </span>
                                @else
                                    <span class="badge-status-inactive">
                                        <i class="fa-solid fa-circle fa-2xs me-1" style="color: #dc2626;"></i> Inactivo
                                    </span>
                                @endif
                            </td>

                            {{-- Botones de Acciones --}}
                            <td class="text-center text-nowrap pe-4">
                                <div class="d-inline-flex gap-1 justify-content-center">
                                    {{-- Botón Ver --}}
                                    <button type="button"
                                            class="btn-action-icon btn-action-view"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalVerProducto{{ $producto->id }}"
                                            title="Ver detalle del producto">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>

                                    {{-- Botón Editar --}}
                                    <button type="button"
                                            class="btn-action-icon btn-action-edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditarProducto{{ $producto->id }}"
                                            title="Editar producto">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    {{-- Botón Eliminar --}}
                                    <button type="button"
                                            class="btn-action-icon btn-action-delete"
                                            onclick="confirmarEliminarProducto({{ $producto->id }}, '{{ addslashes($producto->producto) }}')"
                                            title="Eliminar producto">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>

                                {{-- Formulario oculto para eliminar --}}
                                <form id="formEliminarProducto{{ $producto->id }}"
                                      action="{{ route('producto.destroy', $producto->id) }}"
                                      method="POST"
                                      class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>

                        {{-- Modal: Ver Producto --}}
                        <div class="modal fade"
                             id="modalVerProducto{{ $producto->id }}"
                             tabindex="-1"
                             aria-hidden="true">

                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0 rounded-4 shadow">

                                    <div class="modal-header border-bottom px-4 py-3">
                                        <div>
                                            <h5 class="modal-title fw-bold">
                                                <i class="fa-solid fa-box-open me-2 text-primary"></i>
                                                Detalle del Producto
                                            </h5>
                                            <p class="text-muted small mb-0 mt-1">
                                                Información registrada en FactuWeb PRO.
                                            </p>
                                        </div>
                                        <button type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal"
                                                aria-label="Cerrar"></button>
                                    </div>

                                    <div class="modal-body p-4">
                                        {{-- Fila cabecera del modal: Imagen y título --}}
                                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-4 mb-4">
                                            @if ($producto->imagen_url)
                                                <img src="{{ $producto->imagen_url }}"
                                                     alt="{{ $producto->producto }}"
                                                     width="70"
                                                     height="70"
                                                     class="rounded-3 border object-fit-cover shadow-sm bg-white"
                                                     onerror="this.onerror=null; this.src='https://placehold.co/70x70?text=Foto';">
                                            @else
                                                <div class="bg-white border rounded-3 d-flex align-items-center justify-content-center shadow-sm text-secondary"
                                                     style="width: 70px; height: 70px;">
                                                    <i class="fa-solid fa-box-open fs-2"></i>
                                                </div>
                                            @endif

                                            <div>
                                                <h5 class="fw-bold mb-1 text-dark">{{ $producto->producto }}</h5>
                                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                                    <span class="badge rounded-pill bg-white text-secondary border px-2 py-1 small font-monospace">
                                                        <i class="fa-solid fa-barcode me-1"></i>{{ $producto->codigo_barras ?: 'Sin código' }}
                                                    </span>
                                                    <span class="badge rounded-pill bg-white text-primary border px-2 py-1 small">
                                                        <i class="fa-solid fa-tag me-1"></i>{{ $producto->categoria?->categoria ?? 'Sin categoría' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Rejilla de información --}}
                                        <div class="row g-3">
                                            <div class="col-sm-6">
                                                <div class="p-3 bg-light rounded-3">
                                                    <small class="text-muted d-block mb-1">Estado</small>
                                                    @if ($producto->state)
                                                        <span class="badge-status-active">
                                                            <i class="fa-solid fa-circle fa-2xs me-1" style="color: #16a34a;"></i> Activo
                                                        </span>
                                                    @else
                                                        <span class="badge-status-inactive">
                                                            <i class="fa-solid fa-circle fa-2xs me-1" style="color: #dc2626;"></i> Inactivo
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <div class="p-3 bg-light rounded-3">
                                                    <small class="text-muted d-block mb-1">Stock Disponible</small>
                                                    @if ($producto->stock <= 0)
                                                        <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fw-semibold small">
                                                            <i class="fa-solid fa-triangle-exclamation me-1"></i> Agotado (0 unids.)
                                                        </span>
                                                    @elseif ($producto->stock <= 5)
                                                        <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 fw-semibold small">
                                                            <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $producto->stock }} unids. (Stock bajo)
                                                        </span>
                                                    @else
                                                        <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-semibold small">
                                                            <i class="fa-solid fa-circle-check me-1"></i> {{ $producto->stock }} unidades
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-sm-4">
                                                <div class="p-3 bg-light rounded-3">
                                                    <small class="text-muted d-block mb-1">Precio de Compra</small>
                                                    <span class="fw-semibold text-secondary fs-6">
                                                        ${{ number_format((float) $producto->precio_compra, 2, ',', '.') }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="col-sm-4">
                                                <div class="p-3 bg-light rounded-3">
                                                    <small class="text-muted d-block mb-1">Precio de Venta</small>
                                                    <span class="fw-bold text-success fs-6">
                                                        ${{ number_format((float) $producto->precio_venta, 2, ',', '.') }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="col-sm-4">
                                                <div class="p-3 bg-light rounded-3">
                                                    <small class="text-muted d-block mb-1">IVA</small>
                                                    <span class="fw-semibold text-dark fs-6">
                                                        {{ number_format((float) $producto->iva, 2, ',', '.') }}%
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="p-3 bg-light rounded-3">
                                                    <small class="text-muted d-block mb-1">Margen Estimado de Ganancia</small>
                                                    @php
                                                        $margen = (float) $producto->precio_venta - (float) $producto->precio_compra;
                                                    @endphp
                                                    <span class="fw-bold {{ $margen >= 0 ? 'text-primary' : 'text-danger' }}">
                                                        ${{ number_format($margen, 2, ',', '.') }} por unidad
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer border-top px-4 py-3">
                                        <button type="button"
                                                class="btn btn-light border px-4"
                                                data-bs-dismiss="modal">
                                            Cerrar
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- Modal: Editar Producto --}}
                        <div class="modal fade"
                             id="modalEditarProducto{{ $producto->id }}"
                             tabindex="-1"
                             aria-hidden="true">

                            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content border-0 rounded-4 shadow">

                                    <form action="{{ route('producto.update', $producto->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-header border-bottom px-4 py-3">
                                            <div>
                                                <h5 class="modal-title fw-bold">
                                                    <i class="fa-solid fa-pen-to-square me-2 text-primary"></i>
                                                    Editar Producto
                                                </h5>
                                                <p class="text-muted small mb-0 mt-1">
                                                    Actualiza los datos del producto seleccionado.
                                                </p>
                                            </div>
                                            <button type="button"
                                                    class="btn-close"
                                                    data-bs-dismiss="modal"
                                                    aria-label="Cerrar"></button>
                                        </div>

                                        <div class="modal-body p-4">
                                            <div class="row g-3">

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">
                                                        Nombre del producto <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text"
                                                           name="producto"
                                                           class="form-control"
                                                           maxlength="200"
                                                           value="{{ $producto->producto }}"
                                                           required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">
                                                        Categoría <span class="text-danger">*</span>
                                                    </label>
                                                    <select name="id_categoria"
                                                            class="form-select"
                                                            required>
                                                        <option value="">Selecciona una categoría</option>
                                                        @foreach ($categorias as $categoria)
                                                            <option value="{{ $categoria->id_categoria }}"
                                                                @selected($producto->id_categoria == $categoria->id_categoria)>
                                                                {{ $categoria->categoria }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">
                                                        Código de barras
                                                    </label>
                                                    <input type="text"
                                                           name="codigo_barras"
                                                           class="form-control font-monospace"
                                                           maxlength="100"
                                                           value="{{ $producto->codigo_barras }}"
                                                           placeholder="Código opcional">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">
                                                        URL de la imagen
                                                    </label>
                                                    <input type="url"
                                                           name="imagen_url"
                                                           class="form-control"
                                                           maxlength="500"
                                                           value="{{ $producto->imagen_url }}"
                                                           placeholder="https://ejemplo.com/imagen.jpg">
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold">
                                                        Precio de compra <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number"
                                                               name="precio_compra"
                                                               class="form-control"
                                                               min="0"
                                                               step="0.01"
                                                               value="{{ $producto->precio_compra }}"
                                                               required>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold">
                                                        Precio de venta <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number"
                                                               name="precio_venta"
                                                               class="form-control"
                                                               min="0"
                                                               step="0.01"
                                                               value="{{ $producto->precio_venta }}"
                                                               required>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold">
                                                        Stock disponible <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="number"
                                                           name="stock"
                                                           class="form-control"
                                                           min="0"
                                                           step="1"
                                                           value="{{ $producto->stock }}"
                                                           required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">
                                                        IVA (%) <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="number"
                                                               name="iva"
                                                               class="form-control"
                                                               min="0"
                                                               step="0.01"
                                                               value="{{ $producto->iva }}"
                                                               required>
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">
                                                        Estado <span class="text-danger">*</span>
                                                    </label>
                                                    <select name="state"
                                                            class="form-select"
                                                            required>
                                                        <option value="1" @selected($producto->state)>Activo</option>
                                                        <option value="0" @selected(!$producto->state)>Inactivo</option>
                                                    </select>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="modal-footer border-top px-4 py-3">
                                            <button type="button"
                                                    class="btn btn-light border px-4"
                                                    data-bs-dismiss="modal">
                                                Cancelar
                                            </button>

                                            <button type="submit" class="btn btn-primary-gradient px-4">
                                                <i class="fa-solid fa-floppy-disk me-1"></i>
                                                Guardar cambios
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa-solid fa-box-open fs-1 d-block mb-3 text-secondary opacity-50"></i>
                                    <p class="mb-1 fw-semibold">No se encontraron productos registrados.</p>
                                    <p class="small text-muted mb-0">Comienza registrando un nuevo producto en el botón superior.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if ($productos->hasPages())
            <div class="card-footer bg-white border-top border-light py-3 px-4">
                <div class="d-flex justify-content-end">
                    {{ $productos->links() }}
                </div>
            </div>
        @endif

    </div>

</div>

{{-- Modal: Crear Producto --}}
<div class="modal fade"
     id="modalCrearProducto"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow">

            <form action="{{ route('producto.store') }}" method="POST">
                @csrf

                <div class="modal-header border-bottom px-4 py-3">
                    <div>
                        <h5 class="modal-title fw-bold">
                            <i class="fa-solid fa-box-open me-2 text-primary"></i>
                            Registrar Nuevo Producto
                        </h5>
                        <p class="text-muted small mb-0 mt-1">
                            Completa los datos para ingresar un producto al inventario.
                        </p>
                    </div>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Cerrar"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Nombre del producto <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="producto"
                                   class="form-control"
                                   maxlength="200"
                                   placeholder="Ej. Café molido 500g"
                                   value="{{ old('producto') }}"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Categoría <span class="text-danger">*</span>
                            </label>
                            <select name="id_categoria"
                                    class="form-select"
                                    required>
                                <option value="">Selecciona una categoría</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id_categoria }}" @selected(old('id_categoria') == $categoria->id_categoria)>
                                        {{ $categoria->categoria }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Código de barras
                            </label>
                            <input type="text"
                                   name="codigo_barras"
                                   class="form-control font-monospace"
                                   maxlength="100"
                                   placeholder="Código opcional"
                                   value="{{ old('codigo_barras') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                URL de imagen
                            </label>
                            <input type="url"
                                   name="imagen_url"
                                   class="form-control"
                                   maxlength="500"
                                   placeholder="https://ejemplo.com/imagen.jpg"
                                   value="{{ old('imagen_url') }}">
                            <small class="text-muted">Pega una URL pública de la imagen (opcional).</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                Precio de compra <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number"
                                       name="precio_compra"
                                       class="form-control"
                                       min="0"
                                       step="0.01"
                                       value="{{ old('precio_compra', '0.00') }}"
                                       required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                Precio de venta <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number"
                                       name="precio_venta"
                                       class="form-control"
                                       min="0"
                                       step="0.01"
                                       value="{{ old('precio_venta', '0.00') }}"
                                       required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                Stock inicial <span class="text-danger">*</span>
                            </label>
                            <input type="number"
                                   name="stock"
                                   class="form-control"
                                   min="0"
                                   step="1"
                                   value="{{ old('stock', '0') }}"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                IVA (%) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number"
                                       name="iva"
                                       class="form-control"
                                       min="0"
                                       step="0.01"
                                       value="{{ old('iva', '0.00') }}"
                                       required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Estado <span class="text-danger">*</span>
                            </label>
                            <select name="state" class="form-select" required>
                                <option value="1" @selected(old('state', '1') == '1')>Activo</option>
                                <option value="0" @selected(old('state') === '0')>Inactivo</option>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="modal-footer border-top px-4 py-3">
                    <button type="button"
                            class="btn btn-light border px-4"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-primary-gradient px-4">
                        <i class="fa-solid fa-floppy-disk me-1"></i>
                        Guardar producto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script SweetAlert2 para confirmación de eliminación --}}
<script>
    function confirmarEliminarProducto(id, nombre) {
        Swal.fire({
            title: '¿Eliminar producto?',
            text: `Se eliminará el producto "${nombre}". Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fa-solid fa-trash-can me-1"></i> Sí, eliminar',
            cancelButtonText: 'Cancelar',
            customClass: {
                confirmButton: 'btn btn-danger px-3',
                cancelButton: 'btn btn-light border px-3 ms-2'
            },
            buttonsStyling: false
        }).then((resultado) => {
            if (resultado.isConfirmed) {
                document.getElementById('formEliminarProducto' + id).submit();
            }
        });
    }
</script>

@endsection
