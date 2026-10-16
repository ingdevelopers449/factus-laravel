@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Gestión de Empleados - FactuWeb PRO')

@section('content')
<div class="container-fluid p-0">

    <!-- Encabezado de la Sección -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2" style="color: #12102f;">
                <i class="fa-solid fa-user-shield" style="color: #ea580c;"></i>
                Gestión de Empleados
            </h4>
            <p class="text-muted small mb-0">
                Administra los accesos, roles y usuarios autorizados en FactuWeb PRO.
            </p>
        </div>
        <div>
            <a href="#" class="btn text-white fw-semibold d-inline-flex align-items-center gap-2 px-3 py-2 shadow-sm" style="background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); border-radius: 10px; border: none;">
                <i class="fa-solid fa-user-plus"></i>
                <span>Nuevo Empleado</span>
            </a>
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
            <table class="table align-middle mb-0 table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 text-uppercase text-secondary" style="font-size: 0.75rem; letter-spacing: 0.5px;">Usuario</th>
                        <th class="text-uppercase text-secondary" style="font-size: 0.75rem; letter-spacing: 0.5px;">Correo Electrónico</th>
                        <th class="text-uppercase text-secondary" style="font-size: 0.75rem; letter-spacing: 0.5px;">Rol Asignado</th>
                        <th class="text-uppercase text-secondary" style="font-size: 0.75rem; letter-spacing: 0.5px;">Fecha Registro</th>
                        <th class="text-end pe-4 text-uppercase text-secondary" style="font-size: 0.75rem; letter-spacing: 0.5px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($usuarios as $usuario)
                        @php
                            $role_code = strtoupper($usuario->role?->code ?? '');
                            $is_admin = ($role_code === 'ADMIN');
                            $color_rol = $is_admin ? '#ea580c' : '#10b981';
                            $inicial = strtoupper(substr($usuario->name, 0, 1));
                        @endphp
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm text-white fw-bold flex-shrink-0"
                                         style="width: 38px; height: 38px; background: linear-gradient(135deg, #12102f, #ea580c); font-size: 0.95rem;">
                                        {{ $inicial }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.92rem;">{{ $usuario->name }}</div>
                                        <small class="text-muted">ID: #{{ $usuario->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-secondary small">{{ $usuario->email }}</span>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-3 py-2 text-white fw-semibold" style="background-color: {{ $color_rol }}; font-size: 0.75rem;">
                                    <i class="fa-solid {{ $is_admin ? 'fa-shield-halved' : 'fa-user' }} me-1"></i>
                                    {{ $usuario->role?->name ?? 'Sin Rol' }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted small">
                                    {{ $usuario->created_at ? $usuario->created_at->format('d/m/Y H:i') : 'N/A' }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-light border rounded-2 p-1 me-1" title="Editar" style="width: 32px; height: 32px;">
                                        <i class="fa-solid fa-pen text-primary" style="font-size: 0.8rem;"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-light border rounded-2 p-1" title="Desactivar" style="width: 32px; height: 32px;">
                                        <i class="fa-solid fa-trash text-danger" style="font-size: 0.8rem;"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
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
@endsection
