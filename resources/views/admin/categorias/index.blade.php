
@extends('layouts.sidebaradmin')

@section('titulopagina', 'Gestión de Categorías - FactuWeb PRO')

@section('content')

<div class="container-fluid py-4">

    {{-- Encabezado --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1">Gestión de Categorías</h2>
            <p class="text-muted mb-0">
                Administra las categorías de productos de FactuWeb PRO.
            </p>
        </div>

        <button class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#modalCrearCategoria">
            <i class="bi bi-plus-circle me-1"></i>
            Nueva categoría
        </button>
    </div>

    {{-- Mensajes de éxito --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Errores de validación --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Revisa los siguientes errores:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tabla de categorías --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <h5 class="fw-bold mb-0">Listado de categorías</h5>

                <form action="{{ route('admin.categorias.index') }}"
                      method="GET"
                      class="d-flex gap-2">
                    <input type="search"
                           name="buscar"
                           class="form-control"
                           placeholder="Buscar categoría..."
                           value="{{ request('buscar') }}">

                    <button class="btn btn-outline-primary" type="submit">
                        Buscar
                    </button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Categoría</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Fecha de creación</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($categorias as $categoria)
                            <tr>
                                <td>{{ $categoria->id_categoria }}</td>

                                <td class="fw-semibold">
                                    {{ $categoria->categoria }}
                                </td>

                                <td>
                                    {{ $categoria->descripcion ?: 'Sin descripción' }}
                                </td>

                                <td>
                                    @if ($categoria->state)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </td>

                                <td>
                                    {{ $categoria->created_at?->format('d/m/Y') ?? '—' }}
                                </td>

                                <td class="text-center">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditarCategoria{{ $categoria->id_categoria }}">
                                        <i class="bi bi-pencil-square"></i>
                                        Editar
                                    </button>

                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="confirmarEliminarCategoria({{ $categoria->id_categoria }}, @js($categoria->categoria))">
                                        <i class="bi bi-trash"></i>
                                        Eliminar
                                    </button>

                                    <form id="formEliminarCategoria{{ $categoria->id_categoria }}"
                                          action="{{ route('admin.categorias.destroy', $categoria->id_categoria) }}"
                                          method="POST"
                                          class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>

                            {{-- Modal editar --}}
                            <div class="modal fade"
                                 id="modalEditarCategoria{{ $categoria->id_categoria }}"
                                 tabindex="-1"
                                 aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">

                                        <form action="{{ route('admin.categorias.update', $categoria->id_categoria) }}"
                                              method="POST">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">
                                                    Editar categoría
                                                </h5>
                                                <button type="button"
                                                        class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        Nombre de la categoría
                                                    </label>
                                                    <input type="text"
                                                           name="categoria"
                                                           class="form-control"
                                                           maxlength="100"
                                                           value="{{ $categoria->categoria }}"
                                                           required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Descripción</label>
                                                    <textarea name="descripcion"
                                                              class="form-control"
                                                              rows="3"
                                                              maxlength="255">{{ $categoria->descripcion }}</textarea>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Estado</label>
                                                    <select name="state" class="form-select" required>
                                                        <option value="1" @selected($categoria->state)>Activo</option>
                                                        <option value="0" @selected(!$categoria->state)>Inactivo</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button"
                                                        class="btn btn-light"
                                                        data-bs-dismiss="modal">
                                                    Cancelar
                                                </button>
                                                <button type="submit" class="btn btn-primary">
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
                                    <i class="bi bi-inbox fs-1 text-muted"></i>
                                    <p class="text-muted mt-2 mb-0">
                                        No hay categorías registradas.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $categorias->links() }}
            </div>

        </div>
    </div>
</div>

{{-- Modal crear --}}
<div class="modal fade"
     id="modalCrearCategoria"
     tabindex="-1"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form action="{{ route('admin.categorias.store') }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Nueva categoría</h5>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre de la categoría</label>
                        <input type="text"
                               name="categoria"
                               class="form-control"
                               maxlength="100"
                               placeholder="Ej. Bebidas"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion"
                                  class="form-control"
                                  rows="3"
                                  maxlength="255"
                                  placeholder="Descripción opcional"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select name="state" class="form-select" required>
                            <option value="1" selected>Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Guardar categoría
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- Confirmación de eliminación --}}
<script>
    function confirmarEliminarCategoria(id, nombre) {
        Swal.fire({
            title: '¿Eliminar categoría?',
            text: `La categoría "${nombre}" será eliminada.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((resultado) => {
            if (resultado.isConfirmed) {
                document.getElementById('formEliminarCategoria' + id).submit();
            }
        });
    }
</script>

@endsection
