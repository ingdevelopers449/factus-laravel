@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Gestión de Empleados - FactuWeb PRO')

@section('content')

<div class="container-fluid p-0">

    <!-- Encabezado de la Sección -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2 module-header-title">
                <i class="fa-solid fa-user-shield module-header-icon"></i>
                Gestión de Empleados
            </h4>
            <p class="text-muted small mb-0">
                Administra los accesos, roles y usuarios autorizados en FactuWeb PRO.
            </p>
        </div>
        <div>
            <button type="button"
                    class="btn btn-primary-gradient d-inline-flex align-items-center gap-2 px-3 py-2 shadow-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#modalNuevoEmpleado">
                <i class="fa-solid fa-user-plus"></i>
                <span>Nuevo Empleado</span>
            </button>
        </div>
    </div>

    <!-- Tarjeta Principal con la Tabla -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <!-- Barra de estado y contador -->
        <div class="card-header bg-white border-bottom border-light py-3 px-4 d-flex justify-content-between align-items-center">
            <span class="fw-semibold text-secondary small">
                <i class="fa-solid fa-list-check me-1"></i> Lista de Usuarios Registrados
            </span>
            <span class="badge rounded-pill bg-light text-dark border px-3 py-2 small">
                Total: {{ $usuarios->total() }} registros
            </span>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover table-admin">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Usuario</th>
                        <th>Correo Electrónico</th>
                        <th>Rol Asignado</th>
                        <th>Estado</th>
                        <th>Fecha Registro</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($usuarios as $usuario)
                        @php
                            $role_code = strtoupper($usuario->role?->code ?? '');
                            $is_admin = ($role_code === 'ADMIN');
                            $badge_class = $usuario->role ? ($is_admin ? 'badge-role-admin' : 'badge-role-empleado') : 'badge-role-none';
                            $inicial = strtoupper(substr($usuario->name, 0, 1));
                        @endphp
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="user-avatar-circle flex-shrink-0">
                                        {{ $inicial }}
                                    </div>
                                    <div>
                                        <div class="user-name-title">{{ $usuario->name }}</div>
                                        <small class="text-muted">ID: #{{ $usuario->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-secondary small">{{ $usuario->email }}</span>
                            </td>
                            <td>
                                <span class="{{ $badge_class }}">
                                    <i class="fa-solid {{ $is_admin ? 'fa-shield-halved' : ($usuario->role ? 'fa-user' : 'fa-circle-question') }} me-1"></i>
                                    {{ $usuario->role?->name ?? 'Sin rol asignado' }}
                                </span>
                            </td>
                            <td>
                                @if ($usuario->state)
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
                                    {{ $usuario->created_at ? $usuario->created_at->format('d/m/Y H:i') : 'N/A' }}
                                </span>
                            </td>


                            <!-- BTN DE ACCIONES -->
                            <td class="text-center text-nowrap">
                                <div class="d-inline-flex gap-1 justify-content-center">
                                    <button type="button"
                                            class="btn-action-icon btn-action-view"
                                            data-bs-toggle="modal"
                                            data-bs-target="#verUsuario{{ $usuario->id }}"
                                            title="Ver empleado">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>

                                    <button type="button"
                                            class="btn-action-icon btn-action-edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editarCliente{{ $usuario->id }}"
                                            title="Editar empleado">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <button type="button"
                                            class="btn-action-icon btn-action-delete"
                                            data-bs-toggle="modal"
                                            data-bs-target="#eliminarCliente{{ $usuario->id }}"
                                            title="Eliminar empleado">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- Modal para ver usuario --}}
                        <div class="modal fade"
                            id="verUsuario{{ $usuario->id }}"
                            tabindex="-1"
                            aria-hidden="true">

                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title">Información del usuario</h5>
                                        <button type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal"
                                                aria-label="Cerrar"></button>
                                    </div>

                                    <div class="modal-body">
                                        <p><strong>Nombre:</strong>
                                            {{ $usuario->name }}</p>

                                        <p><strong>Correo:</strong>
                                            {{ $usuario->email }}</p>

                                        <p class="d-flex align-items-center gap-2">
                                            <strong>Rol:</strong>
                                            <span class="{{ $badge_class }}">
                                                <i class="fa-solid {{ $is_admin ? 'fa-shield-halved' : ($usuario->role ? 'fa-user' : 'fa-circle-question') }} me-1"></i>
                                                {{ $usuario->role?->name ?? 'Sin rol asignado' }}
                                            </span>
                                        </p>

                                        <p class="d-flex align-items-center gap-2">
                                            <strong>Estado:</strong>
                                            @if ((bool) $usuario->state)
                                                <span class="badge-status-active">
                                                    <i class="fa-solid fa-circle fa-2xs me-1" style="color: #16a34a;"></i>
                                                    Activo
                                                </span>
                                            @else
                                                <span class="badge-status-inactive">
                                                    <i class="fa-solid fa-circle fa-2xs me-1" style="color: #dc2626;"></i>
                                                    Inactivo
                                                </span>
                                            @endif
                                        </p>

                                        <p><strong>Fecha Registro:</strong>
                                            {{ $usuario->created_at ? $usuario->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button"
                                                class="btn btn-secondary"
                                                data-bs-dismiss="modal">
                                            Cerrar
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- Modal para editar usuario --}}
                        <div class="modal fade"
                            id="editarCliente{{ $usuario->id }}"
                            tabindex="-1"
                            aria-hidden="true">

                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">

                                    <form action="{{ route('usuario.update', $usuario->id) }}"
                                        method="POST">

                                        @csrf
                                        @method('PUT')

                                        <div class="modal-header">
                                            <h5 class="modal-title">Editar Empleado</h5>
                                            <button type="button"
                                                    class="btn-close"
                                                    data-bs-dismiss="modal"
                                                    aria-label="Cerrar"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Nombre *</label>
                                                <input type="text"
                                                    name="name"
                                                    class="form-control"
                                                    maxlength="20"
                                                    value="{{ $usuario->name }}"
                                                    required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Correo *</label>
                                                <input type="email"
                                                    name="email"
                                                    class="form-control"
                                                    maxlength="150"
                                                    value="{{ $usuario->email }}"
                                                    required>
                                            </div>
                                            {{-- Rol del usuario --}}
                                            <div class="mb-3">
                                                <label for="role_id{{ $usuario->id }}" class="form-label">
                                                    Rol del usuario *
                                                </label>

                                                <select
                                                    name="role_id"
                                                    id="role_id{{ $usuario->id }}"
                                                    class="form-select"
                                                    required
                                                >
                                                    @foreach ($roles as $rol)
                                                        <option
                                                            value="{{ $rol->id }}"
                                                            {{ $usuario->role_id == $rol->id ? 'selected' : '' }}
                                                        >
                                                            {{ $rol->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- Estado del usuario --}}
                                            <div class="mb-3">
                                                <label for="state{{ $usuario->id }}" class="form-label">
                                                    Estado del usuario
                                                </label>

                                                <select
                                                    name="state"
                                                    id="state{{ $usuario->id }}"
                                                    class="form-select"
                                                    required
                                                >
                                                    <option value="1" {{ (bool) $usuario->state ? 'selected' : '' }}>
                                                        Activo
                                                    </option>

                                                    <option value="0" {{ !(bool) $usuario->state ? 'selected' : '' }}>
                                                        Inactivo
                                                    </option>
                                                </select>

                                                <small class="text-muted">
                                                    Los usuarios inactivos no deberían poder iniciar sesión.
                                                </small>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button"
                                                    class="btn btn-secondary"
                                                    data-bs-dismiss="modal">
                                                Cancelar
                                            </button>

                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-save"></i> Guardar cambios
                                            </button>
                                        </div>

                                    </form>

                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa-solid fa-users-slash fs-1 d-block mb-3 text-secondary opacity-50"></i>
                                    <p class="mb-0 fw-semibold">No se encontraron usuarios registrados.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($usuarios->hasPages())
            <div class="card-footer bg-white border-top border-light py-3 px-4">
                <div class="d-flex justify-content-end">
                    {{ $usuarios->links() }}
                </div>
            </div>
        @endif
    </div>

</div>


<!-- Modal: Nuevo Empleado -->
<div class="modal fade" id="modalNuevoEmpleado" tabindex="-1" aria-labelledby="tituloModalNuevoEmpleado" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">

            <!-- Encabezado -->
            <div class="modal-header border-bottom px-4 py-3">
                <div>
                    <h5 class="modal-title fw-bold" id="tituloModalNuevoEmpleado">
                        <i class="fa-solid fa-user-plus me-2 text-primary"></i>
                        Nuevo Empleado
                    </h5>
                    <p class="text-muted small mb-0 mt-1">
                        Registra un nuevo usuario en FactuWeb PRO.
                    </p>
                </div>

                <button type="button" class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo -->
            
            
            <div class="modal-body p-4">
                <form id="formNuevoEmpleado"
                    action="{{ route('usuario.store') }}"
                    method="POST">
                    @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">
                                Nombre completo
                            </label>
                            <input type="text" class="form-control"
                                id="name" name="name"
                                placeholder="Ej. Juan Pérez">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">
                                Correo electrónico
                            </label>
                            <input type="email" class="form-control"
                                id="email" name="email"
                                placeholder="usuario@correo.com">
                        </div>

                        <div class="mb-3">
                            <label for="role_id" class="form-label fw-semibold">
                                Rol del usuario
                            </label>
                            <select class="form-select" id="role_id" name="role_id">
                                <option value="">Selecciona un rol</option>
                                @foreach ($roles as $rol)
                                    <option value="{{ $rol->id }}">
                                        {{ $rol->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="state" class="form-label fw-semibold">
                                Estado del usuario
                            </label>
                            <select class="form-select" id="state" name="state">
                                <option value="1" selected>Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">
                                Contraseña
                            </label>
                            <input type="password" class="form-control"
                                id="password" name="password"
                                placeholder="Mínimo 8 caracteres">
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation"
                                class="form-label fw-semibold">
                                Confirmar contraseña
                            </label>
                            <input type="password" class="form-control"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="Repite la contraseña">
                        </div>
                </form>
            </div>
            

            <!-- Pie -->
            <div class="modal-footer border-top px-4 py-3">
                <button type="button" class="btn btn-light border"
                        data-bs-dismiss="modal">
                    Cancelar
                </button>

                <button type="submit" form="formNuevoEmpleado"
                        class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk me-1"></i>
                    Guardar empleado
                </button>
            </div>

        </div>
    </div>
</div>


@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: '¡Registro exitoso!',
                text: {{ Illuminate\Support\Js::from(session('success')) }},
                confirmButtonText: 'Aceptar'
            });
        });
    </script>
@endif

@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'error',
                title: 'Revisa los datos',
                text: {{ Illuminate\Support\Js::from($errors->first()) }},
                confirmButtonText: 'Entendido'
            });
        });
    </script>
@endif



@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'error',
                title: 'Revisa los datos',
                text: {{ Illuminate\Support\Js::from($errors->first()) }},
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#d33'
            });
        });
    </script>
@endif


@endsection
