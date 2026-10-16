@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Gestión de Categorías - FactuWeb PRO')

@section('content')
<div class="container-fluid p-0">

    {{-- Encabezado --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-tags text-primary"></i>
                Gestión de Categorías
            </h4>
            <p class="text-muted small mb-0">
                Administra las categorías de productos de FactuWeb PRO.
            </p>
        </div>

        <button type="button"
                class="btn btn-primary-gradient d-inline-flex align-items-center gap-2 px-3 py-2 shadow-sm"
                data-bs-toggle="modal"
                data-bs-target="#modalCrearCategoria">
            <i class="fa-solid fa-plus"></i>
            <span>Nueva Categoría</span>
        </button>
    </div>

    {{-- Errores de validación --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Revisa los siguientes errores:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Tabla de categorías --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">

        {{-- Cabecera con título/conteo a la izquierda y buscador integrado a la derecha --}}
        <div class="card-header bg-white border-bottom py-3 px-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

                {{-- Título y Badge --}}
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold text-secondary small">
                        <i class="fa-solid fa-list-check me-1"></i> Listado de Categorías
                    </span>
                    <span class="badge rounded-pill bg-light text-dark border px-3 py-1 small">
                        Total: {{ $categorias->total() }} registros
                    </span>
                </div>

                {{-- Buscador Integrado --}}
                <form action="{{ route('admin.categorias.index') }}" method="GET" class="m-0" style="min-width: 280px; max-width: 420px; width: 100%;">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="search"
                               name="buscar"
                               class="form-control bg-light border-start-0 ps-0"
                               placeholder="Buscar categoría o descripción..."
                               value="{{ $buscar ?? request('buscar') }}">
                        <button type="submit" class="btn btn-primary-gradient px-3">
                            Buscar
                        </button>
                        @if ($buscar ?? request('buscar'))
                            <a href="{{ route('admin.categorias.index') }}" class="btn btn-light border" title="Limpiar búsqueda">
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
                        <th class="ps-4">ID</th>
                        <th>Categoría</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Fecha de Creación</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($categorias as $categoria)
                        <tr>
                            <td class="ps-4 py-3">
                                <span class="fw-semibold text-muted">#{{ $categoria->id_categoria }}</span>
                            </td>

                            <td class="fw-semibold text-dark">
                                {{ $categoria->categoria }}
                            </td>

                            <td>
                                <span class="text-secondary small">
                                    {{ $categoria->descripcion ?: 'Sin descripción' }}
                                </span>
                            </td>

                            <td>
                                @if ($categoria->state)
                                    <span class="badge-status-active">
                                        <i class="fa-solid fa-circle fa-2xs me-1" style="color: #16a34a;"></i> Activo
                                    </span>
                                @else
                                    <span class="badge-status-inactive">
                                        <i class="fa-solid fa-circle fa-2xs me-1" style="color: #dc2626;"></i> Inactivo
                                    </span>
                                @endif
                            </td>

                            <td>
                                <span class="text-muted small">
                                    {{ $categoria->created_at?->format('d/m/Y') ?? '—' }}
                                </span>
                            </td>

                            <td class="text-center text-nowrap">
                                <div class="d-inline-flex gap-1 justify-content-center">
                                    <button type="button"
                                            class="btn-action-icon btn-action-edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditarCategoria{{ $categoria->id_categoria }}"
                                            title="Editar categoría">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <button type="button"
                                            class="btn-action-icon btn-action-delete"
                                            onclick="confirmarEliminarCategoria({{ $categoria->id_categoria }}, @js($categoria->categoria))"
                                            title="Eliminar categoría">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>

                                <form id="formEliminarCategoria{{ $categoria->id_categoria }}"
                                      action="{{ route('admin.categorias.destroy', $categoria->id_categoria) }}"
                                      method="POST"
                                      class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>

                        {{-- Modal editar categoría --}}
                        <div class="modal fade"
                             id="modalEditarCategoria{{ $categoria->id_categoria }}"
                             tabindex="-1"
                             aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 rounded-4 shadow">

                                    <form action="{{ route('admin.categorias.update', $categoria->id_categoria) }}"
                                          method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-header border-bottom px-4 py-3">
                                            <h5 class="modal-title fw-bold">
                                                Editar categoría
                                            </h5>
                                            <button type="button"
                                                    class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body px-4 py-3">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">
                                                    Nombre de la categoría *
                                                </label>
                                                <input type="text"
                                                       name="categoria"
                                                       class="form-control"
                                                       maxlength="100"
                                                       value="{{ $categoria->categoria }}"
                                                       required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Descripción</label>
                                                <textarea name="descripcion"
                                                          class="form-control"
                                                          rows="3"
                                                          maxlength="255">{{ $categoria->descripcion }}</textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Estado *</label>
                                                <select name="state" class="form-select" required>
                                                    <option value="1" @selected($categoria->state)>Activo</option>
                                                    <option value="0" @selected(!$categoria->state)>Inactivo</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="modal-footer border-top px-4 py-3">
                                            <button type="button"
                                                    class="btn btn-light border"
                                                    data-bs-dismiss="modal">
                                                Cancelar
                                            </button>
                                            <button type="submit" class="btn btn-primary-gradient">
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
                            <td colspan="6" class="text-center py-5">
                                <i class="fa-solid fa-tags fs-1 d-block mb-3 text-secondary opacity-50"></i>
                                <p class="mb-1 fw-semibold text-dark">
                                    No hay categorías registradas.
                                </p>
                                <p class="text-muted small mb-0">
                                    Cuando registres una categoría, aparecerá en esta tabla.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if ($categorias->hasPages())
            <div class="card-footer bg-white border-top py-3 px-4">
                <div class="d-flex justify-content-end">
                    {{ $categorias->links() }}
                </div>
            </div>
        @endif

    </div>
</div>

{{-- Modal crear categoría --}}
<div class="modal fade"
     id="modalCrearCategoria"
     tabindex="-1"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">

            <form action="{{ route('admin.categorias.store') }}" method="POST">
                @csrf

                <div class="modal-header border-bottom px-4 py-3">
                    <h5 class="modal-title fw-bold">Nueva categoría</h5>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre de la categoría *</label>
                        <input type="text"
                               name="categoria"
                               class="form-control"
                               maxlength="100"
                               placeholder="Ej. Bebidas, Abarrotes, etc."
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea name="descripcion"
                                  class="form-control"
                                  rows="3"
                                  maxlength="255"
                                  placeholder="Descripción opcional"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Estado *</label>
                        <select name="state" class="form-select" required>
                            <option value="1" selected>Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer border-top px-4 py-3">
                    <button type="button"
                            class="btn btn-light border"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary-gradient">
                        <i class="fa-solid fa-floppy-disk me-1"></i>
                        Guardar categoría
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- Confirmación de eliminación con SweetAlert2 --}}
<script>
    function confirmarEliminarCategoria(id, nombre) {
        Swal.fire({
            title: '¿Eliminar categoría?',
            text: `La categoría "${nombre}" será eliminada.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((resultado) => {
            if (resultado.isConfirmed) {
                document.getElementById('formEliminarCategoria' + id).submit();
            }
        });
    }
</script>

{{-- Alerta de éxito con SweetAlert2 --}}
@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: '¡Operación exitosa!',
                text: {{ Illuminate\Support\Js::from(session('success')) }},
                confirmButtonText: 'Aceptar'
            });
        });
    </script>
@endif

@endsection
