@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Parametrización DIAN - FactuWeb PRO')

@section('content')
<div class="container-fluid p-0">

    {{-- Encabezado Principal --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-building-columns text-primary"></i>
                Parametrización DIAN
            </h4>
            <p class="text-muted small mb-0">
                Administra los rangos de numeración, prefijos y resoluciones autorizadas por la DIAN para facturación electrónica.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button"
                    class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3 py-2 shadow-sm rounded-3"
                    data-bs-toggle="modal"
                    data-bs-target="#modalConfigTecnica">
                <i class="fa-solid fa-sliders"></i>
                <span>Configuración Técnica</span>
            </button>
            <button type="button"
                    class="btn btn-primary-gradient d-inline-flex align-items-center gap-2 px-3 py-2 shadow-sm rounded-3"
                    data-bs-toggle="modal"
                    data-bs-target="#modalCrearResolucion">
                <i class="fa-solid fa-plus"></i>
                <span>Nueva Resolución</span>
            </button>
        </div>
    </div>

    {{-- Alertas de Errores de Validación --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="fa-solid fa-circle-exclamation fs-5 text-danger"></i>
                <strong class="text-danger">Por favor corrige los siguientes inconvenientes:</strong>
            </div>
            <ul class="mb-0 ps-3 small text-danger">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    {{-- Tarjetas de Métricas Rápidas (KPIs) --}}
    <div class="row g-3 mb-4">
        {{-- Tarjeta 1: Resolución Activa --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Resolución Activa</span>
                    @if ($resolucionActiva)
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small fw-semibold">
                            <i class="fa-solid fa-circle fa-2xs me-1"></i> Vigente
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2 py-1 small">
                            Sin Activa
                        </span>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-file-shield fs-4"></i>
                    </div>
                    <div class="text-truncate">
                        @if ($resolucionActiva)
                            <h5 class="fw-bold mb-0 text-dark text-truncate">{{ $resolucionActiva->prefijo }} · {{ $resolucionActiva->numero_resolucion }}</h5>
                            <p class="text-muted small mb-0 text-truncate">Prefijo en facturación</p>
                        @else
                            <h6 class="fw-bold mb-0 text-dark">No configurada</h6>
                            <p class="text-muted small mb-0">Registra una resolución</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Tarjeta 2: Consecutivo y Rango --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Consumo de Rango</span>
                    <span class="badge bg-light text-secondary border rounded-pill px-2 py-1 small">
                        @if ($resolucionActiva)
                            {{ number_format($resolucionActiva->rango_inicial, 0, ',', '.') }} al {{ number_format($resolucionActiva->rango_final, 0, ',', '.') }}
                        @else
                            0 al 0
                        @endif
                    </span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-arrow-up-1-9 fs-4"></i>
                    </div>
                    <div class="flex-grow-1">
                        @if ($resolucionActiva)
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h5 class="fw-bold mb-0 text-dark"># {{ number_format($resolucionActiva->contador_actual, 0, ',', '.') }}</h5>
                                <span class="text-muted small">{{ $resolucionActiva->porcentajeConsumido() }}%</span>
                            </div>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar bg-success rounded-pill"
                                     role="progressbar"
                                     style="width: {{ $resolucionActiva->porcentajeConsumido() }}%;"
                                     aria-valuenow="{{ $resolucionActiva->porcentajeConsumido() }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100"></div>
                            </div>
                        @else
                            <h5 class="fw-bold mb-0 text-dark"># 0</h5>
                            <span class="text-muted small">0% consumido</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Tarjeta 3: Días de Vigencia Restantes --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Vigencia DIAN</span>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-1 small">
                        @if ($resolucionActiva && $resolucionActiva->fecha_vigencia)
                            Vence: {{ $resolucionActiva->fecha_vigencia->format('d/m/Y') }}
                        @else
                            Sin fecha
                        @endif
                    </span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-warning-subtle text-warning-emphasis d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-calendar-check fs-4"></i>
                    </div>
                    <div>
                        @if ($resolucionActiva)
                            <h5 class="fw-bold mb-0 text-dark">{{ $resolucionActiva->diasRestantes() }} días</h5>
                            <p class="text-muted small mb-0">Tiempo restante autorizado</p>
                        @else
                            <h6 class="fw-bold mb-0 text-dark">—</h6>
                            <p class="text-muted small mb-0">Sin vigencia activa</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Tarjeta 4: Empresa Emisora --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Empresa Emisora</span>
                    <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-1 small fw-semibold">
                        NIT: {{ $empresa->nit ?: 'No reg.' }}
                    </span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-info-subtle text-info d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-building fs-4"></i>
                    </div>
                    <div class="text-truncate">
                        <h6 class="fw-bold mb-0 text-dark text-truncate">{{ $empresa->razon_social ?: 'Configurar Empresa' }}</h6>
                        <p class="text-muted small mb-0 text-truncate">{{ $empresa->email ?: 'Sin correo registrado' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs de Navegación --}}
    <ul class="nav nav-pills gap-2 mb-4 p-1 bg-white rounded-4 shadow-sm border d-inline-flex" id="dianTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-3 px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2"
                    id="tab-resoluciones-btn"
                    data-bs-toggle="pill"
                    data-bs-target="#tab-resoluciones"
                    type="button"
                    role="tab">
                <i class="fa-solid fa-stamp"></i>
                <span>Resoluciones Autorizadas</span>
                <span class="badge bg-primary text-white rounded-pill px-2 py-0 ms-1 small">{{ $resoluciones->total() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3 px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2 text-secondary"
                    id="tab-parametros-btn"
                    data-bs-toggle="pill"
                    data-bs-target="#tab-parametros"
                    type="button"
                    role="tab">
                <i class="fa-solid fa-key"></i>
                <span>Parámetros de Conexión</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3 px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2 text-secondary"
                    id="tab-documentos-btn"
                    data-bs-toggle="pill"
                    data-bs-target="#tab-documentos"
                    type="button"
                    role="tab">
                <i class="fa-solid fa-folder-tree"></i>
                <span>Tipos de Documento</span>
            </button>
        </li>
    </ul>

    {{-- Contenido de Tabs --}}
    <div class="tab-content" id="dianTabsContent">

        {{-- TAB 1: Resoluciones de Facturación --}}
        <div class="tab-pane fade show active" id="tab-resoluciones" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                {{-- Cabecera de la tabla --}}
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-semibold text-secondary small">
                                <i class="fa-solid fa-list-check me-1"></i> Listado de Resoluciones DIAN
                            </span>
                            <span class="badge rounded-pill bg-light text-dark border px-3 py-1 small">
                                Total: {{ $resoluciones->total() }} registros
                            </span>
                        </div>

                        {{-- Buscador Integrado --}}
                        <form action="{{ route('parametrizacion.index') }}" method="GET" class="m-0" style="min-width: 280px; max-width: 420px; width: 100%;">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </span>
                                <input type="search"
                                       name="buscar"
                                       class="form-control bg-light border-start-0 ps-0"
                                       placeholder="Buscar por resolución o prefijo..."
                                       value="{{ $buscar ?? '' }}">
                                <button type="submit" class="btn btn-primary-gradient px-3">
                                    Buscar
                                </button>
                                @if ($buscar)
                                    <a href="{{ route('parametrizacion.index') }}" class="btn btn-light border" title="Limpiar búsqueda">
                                        <i class="fa-solid fa-xmark text-secondary"></i>
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Tabla responsiva --}}
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-admin">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Prefijo</th>
                                <th>N° de Resolución</th>
                                <th>Fecha Vigencia</th>
                                <th>Rango Autorizado</th>
                                <th>Consecutivo Actual</th>
                                <th>Estado</th>
                                <th class="text-center pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($resoluciones as $resolucion)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <span class="fw-semibold text-muted">#{{ $resolucion->id_resolucion }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-2 fw-bold">
                                            {{ $resolucion->prefijo }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $resolucion->numero_resolucion }}</div>
                                        <div class="text-muted small">
                                            Folios: {{ number_format($resolucion->totalFolios(), 0, ',', '.') }} autorizados
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-dark small fw-semibold">
                                            {{ $resolucion->fecha_vigencia ? $resolucion->fecha_vigencia->format('d/m/Y') : '—' }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.75rem;">
                                            @if ($resolucion->diasRestantes() > 0)
                                                {{ $resolucion->diasRestantes() }} días restantes
                                            @else
                                                <span class="text-danger fw-semibold">Vencida</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1 text-secondary small">
                                            <span class="badge bg-light text-dark border">{{ number_format($resolucion->rango_inicial, 0, ',', '.') }}</span>
                                            <span>al</span>
                                            <span class="badge bg-light text-dark border">{{ number_format($resolucion->rango_final, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="text-muted mt-1" style="font-size: 0.75rem;">
                                            Disponibles: {{ number_format($resolucion->facturasDisponibles(), 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-success"># {{ number_format($resolucion->contador_actual, 0, ',', '.') }}</div>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <div class="progress flex-grow-1" style="height: 4px; width: 80px;">
                                                <div class="progress-bar bg-success" style="width: {{ $resolucion->porcentajeConsumido() }}%;"></div>
                                            </div>
                                            <span class="text-muted" style="font-size: 0.7rem;">{{ $resolucion->porcentajeConsumido() }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($resolucion->estado === 'activa')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 small fw-semibold">
                                                <i class="fa-solid fa-circle fa-2xs me-1"></i> Activa
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-3 py-1 small fw-semibold">
                                                <i class="fa-solid fa-circle fa-2xs me-1"></i> Inactiva
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-4 text-nowrap">
                                        <div class="d-inline-flex gap-1 justify-content-center">
                                            {{-- Botón Editar --}}
                                            <button type="button"
                                                    class="btn btn-sm btn-light border text-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalEditarResolucion{{ $resolucion->id_resolucion }}"
                                                    title="Editar resolución">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>

                                            {{-- Botón Ver Detalles --}}
                                            <button type="button"
                                                    class="btn btn-sm btn-light border text-info"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalDetallesResolucion{{ $resolucion->id_resolucion }}"
                                                    title="Ver detalles">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>

                                            {{-- Botón Alternar Estado (Activar / Inactivar) --}}
                                            <form action="{{ route('parametrizacion.toggleEstado', $resolucion->id_resolucion) }}"
                                                  method="POST"
                                                  class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="btn btn-sm btn-light border {{ $resolucion->estado === 'activa' ? 'text-warning' : 'text-success' }}"
                                                        title="{{ $resolucion->estado === 'activa' ? 'Inactivar resolución' : 'Activar resolución' }}">
                                                    <i class="fa-solid {{ $resolucion->estado === 'activa' ? 'fa-pause' : 'fa-play' }}"></i>
                                                </button>
                                            </form>

                                            {{-- Botón Eliminar --}}
                                            <button type="button"
                                                    class="btn btn-sm btn-light border text-danger"
                                                    onclick="confirmarEliminarResolucion({{ $resolucion->id_resolucion }}, @js($resolucion->prefijo . ' - ' . $resolucion->numero_resolucion))"
                                                    title="Eliminar resolución">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>

                                        {{-- Formulario oculto para eliminar --}}
                                        <form id="formEliminarResolucion{{ $resolucion->id_resolucion }}"
                                              action="{{ route('parametrizacion.destroy', $resolucion->id_resolucion) }}"
                                              method="POST"
                                              class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>

                                {{-- =====================================================
                                     MODAL EDITAR RESOLUCIÓN (INDIVIDUAL)
                                     ===================================================== --}}
                                <div class="modal fade"
                                     id="modalEditarResolucion{{ $resolucion->id_resolucion }}"
                                     tabindex="-1"
                                     aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content border-0 rounded-4 shadow">

                                            <div class="modal-header border-bottom px-4 py-3 bg-light rounded-top-4">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="rounded-3 p-2 bg-primary text-white d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </div>
                                                    <div>
                                                        <h5 class="modal-title fw-bold mb-0 text-dark">Editar Resolución DIAN</h5>
                                                        <p class="text-muted small mb-0">Modifica los parámetros autorizados para el prefijo <strong>{{ $resolucion->prefijo }}</strong>.</p>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                            </div>

                                            <form action="{{ route('parametrizacion.update', $resolucion->id_resolucion) }}" method="POST">
                                                @csrf
                                                @method('PUT')

                                                <div class="modal-body px-4 py-3">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-semibold">Número de Resolución DIAN *</label>
                                                            <input type="text"
                                                                   name="numero_resolucion"
                                                                   class="form-control"
                                                                   value="{{ old('numero_resolucion', $resolucion->numero_resolucion) }}"
                                                                   required>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-semibold">Prefijo Autorizado *</label>
                                                            <input type="text"
                                                                   name="prefijo"
                                                                   class="form-control text-uppercase"
                                                                   value="{{ old('prefijo', $resolucion->prefijo) }}"
                                                                   maxlength="20"
                                                                   required>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-semibold">Rango Inicial (Desde) *</label>
                                                            <input type="number"
                                                                   name="rango_inicial"
                                                                   class="form-control"
                                                                   value="{{ old('rango_inicial', $resolucion->rango_inicial) }}"
                                                                   min="1"
                                                                   required>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-semibold">Rango Final (Hasta) *</label>
                                                            <input type="number"
                                                                   name="rango_final"
                                                                   class="form-control"
                                                                   value="{{ old('rango_final', $resolucion->rango_final) }}"
                                                                   min="1"
                                                                   required>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-semibold">Fecha Límite de Vigencia *</label>
                                                            <input type="date"
                                                                   name="fecha_vigencia"
                                                                   class="form-control"
                                                                   value="{{ old('fecha_vigencia', $resolucion->fecha_vigencia ? $resolucion->fecha_vigencia->format('Y-m-d') : '') }}"
                                                                   required>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-semibold">Consecutivo Actual / Contador *</label>
                                                            <input type="number"
                                                                   name="contador_actual"
                                                                   class="form-control"
                                                                   value="{{ old('contador_actual', $resolucion->contador_actual) }}"
                                                                   min="0"
                                                                   required>
                                                            <div class="form-text small text-muted">Último número de folio emitido.</div>
                                                        </div>

                                                        <div class="col-12">
                                                            <label class="form-label small fw-semibold">Estado de la Resolución *</label>
                                                            <select name="estado" class="form-select" required>
                                                                <option value="activa" @selected(old('estado', $resolucion->estado) === 'activa')>Activa (Habilitada para emitir)</option>
                                                                <option value="inactiva" @selected(old('estado', $resolucion->estado) === 'inactiva')>Inactiva (En reserva / Pausada)</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal-footer border-top px-4 py-3 bg-light rounded-bottom-4">
                                                    <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal">
                                                        Cancelar
                                                    </button>
                                                    <button type="submit" class="btn btn-primary-gradient px-4">
                                                        <i class="fa-solid fa-floppy-disk me-1"></i> Guardar Cambios
                                                    </button>
                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                </div>

                                {{-- =====================================================
                                     MODAL DETALLES DE RESOLUCIÓN (INDIVIDUAL)
                                     ===================================================== --}}
                                <div class="modal fade"
                                     id="modalDetallesResolucion{{ $resolucion->id_resolucion }}"
                                     tabindex="-1"
                                     aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 rounded-4 shadow">
                                            <div class="modal-header border-bottom px-4 py-3 bg-light rounded-top-4">
                                                <h6 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                                                    <i class="fa-solid fa-circle-info text-primary"></i>
                                                    Detalle de Resolución {{ $resolucion->prefijo }} - {{ $resolucion->numero_resolucion }}
                                                </h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="text-center pb-3 border-bottom mb-3">
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill fw-bold fs-6 mb-2">
                                                        Prefijo: {{ $resolucion->prefijo }}
                                                    </span>
                                                    <h5 class="fw-bold mb-0 text-dark">Resolución N° {{ $resolucion->numero_resolucion }}</h5>
                                                    <p class="text-muted small mb-0">{{ $empresa->razon_social ?: 'Empresa Emisora' }}</p>
                                                </div>

                                                <div class="row g-2 small">
                                                    <div class="col-6 text-muted">Rango autorizado:</div>
                                                    <div class="col-6 fw-bold text-end">{{ number_format($resolucion->rango_inicial, 0, ',', '.') }} al {{ number_format($resolucion->rango_final, 0, ',', '.') }}</div>

                                                    <div class="col-6 text-muted">Total folios autorizados:</div>
                                                    <div class="col-6 fw-bold text-end">{{ number_format($resolucion->totalFolios(), 0, ',', '.') }}</div>

                                                    <div class="col-6 text-muted">Facturas emitidas:</div>
                                                    <div class="col-6 fw-bold text-end text-success">{{ number_format($resolucion->facturasEmitidas(), 0, ',', '.') }}</div>

                                                    <div class="col-6 text-muted">Facturas disponibles:</div>
                                                    <div class="col-6 fw-bold text-end text-primary">{{ number_format($resolucion->facturasDisponibles(), 0, ',', '.') }}</div>

                                                    <div class="col-6 text-muted">Consecutivo actual:</div>
                                                    <div class="col-6 fw-semibold text-end">#{{ number_format($resolucion->contador_actual, 0, ',', '.') }}</div>

                                                    <div class="col-6 text-muted">Fecha de vigencia:</div>
                                                    <div class="col-6 fw-semibold text-end">{{ $resolucion->fecha_vigencia ? $resolucion->fecha_vigencia->format('d/m/Y') : '—' }}</div>

                                                    <div class="col-6 text-muted">Días restantes:</div>
                                                    <div class="col-6 fw-semibold text-end text-warning-emphasis">{{ $resolucion->diasRestantes() }} días</div>

                                                    <div class="col-6 text-muted">Estado actual:</div>
                                                    <div class="col-6 text-end">
                                                        @if ($resolucion->estado === 'activa')
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0">Activa</span>
                                                        @else
                                                            <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2 py-0">Inactiva</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top px-4 py-3 bg-light rounded-bottom-4">
                                                <button type="button" class="btn btn-secondary w-100 rounded-3" data-bs-dismiss="modal">
                                                    Cerrar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="text-secondary opacity-50 mb-3">
                                            <i class="fa-solid fa-stamp fs-1"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">No hay resoluciones DIAN registradas</h6>
                                        <p class="text-muted small mb-3">
                                            {{ $buscar ? 'No se encontraron resoluciones que coincidan con la búsqueda.' : 'Registra la primera resolución para habilitar la numeración oficial de comprobantes.' }}
                                        </p>
                                        <button type="button"
                                                class="btn btn-primary-gradient px-3 py-2 btn-sm rounded-3 shadow-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalCrearResolucion">
                                            <i class="fa-solid fa-plus me-1"></i> Registrar Resolución
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pie de tabla con Paginación --}}
                @if ($resoluciones->hasPages())
                    <div class="card-footer bg-white border-top py-3 px-4 d-flex justify-content-end">
                        {{ $resoluciones->links() }}
                    </div>
                @else
                    <div class="card-footer bg-white border-top py-3 px-4 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                        <span class="text-muted small">
                            <i class="fa-solid fa-circle-info text-primary me-1"></i>
                            Las resoluciones son emitidas y autorizadas formalmente a través del portal <strong>MUISCA</strong> de la DIAN.
                        </span>
                        <span class="badge bg-light text-secondary border px-3 py-2 small">
                            Total: {{ $resoluciones->total() }} registros
                        </span>
                    </div>
                @endif
            </div>
        </div>

        {{-- TAB 2: Parámetros de Conexión Técnica --}}
        <div class="tab-pane fade" id="tab-parametros" role="tabpanel">
            <div class="row g-4">
                {{-- Columna 1: Credenciales Web Services DIAN --}}
                <div class="col-12 col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                        <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                <i class="fa-solid fa-network-wired text-primary"></i>
                                Configuración de Servicio Web DIAN (v2.1)
                            </h6>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 small">
                                UBL 2.1
                            </span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Ambiente de Operación</label>
                                <select class="form-select bg-light" disabled>
                                    <option selected>1 - Producción (Facturación Oficial)</option>
                                    <option>2 - Habilitación (Pruebas / Set de Validación)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">TestSetId (Set de Pruebas)</label>
                                <input type="text" class="form-control bg-light" value="b2c86e09-6c84-48f1-9d56-787db12f60a9" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Software ID</label>
                                <input type="text" class="form-control bg-light" value="846c4f92-5db1-4cfa-bb01-209e99cfa110" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">PIN de Software</label>
                                <input type="password" class="form-control bg-light" value="•••••" readonly>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">Clave Técnica DIAN</label>
                                <div class="input-group">
                                    <input type="password" class="form-control bg-light" id="inputClaveTecnica" value="fc8eac422eba16e22ffd8c6f94b3f40a6e38162c" readonly>
                                    <button class="btn btn-light border text-secondary" type="button" onclick="toggleVerPassword('inputClaveTecnica', this)" title="Mostrar/Ocultar">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text small text-muted">
                                    Clave técnica asignada por la DIAN para generar la huella de firma digital CUFE.
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary">URL Endpoint de Envío DIAN</label>
                                <input type="text" class="form-control bg-light font-monospace small" value="https://vpfe.dian.gov.co/WcfDianCustomerServices.svc" readonly>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-primary-gradient px-4" data-bs-toggle="modal" data-bs-target="#modalConfigTecnica">
                                <i class="fa-solid fa-pen-to-square me-1"></i> Editar Parámetros
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Columna 2: Certificado Digital y Estado --}}
                <div class="col-12 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                        <div class="border-bottom pb-3 mb-3">
                            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                <i class="fa-solid fa-certificate text-warning"></i>
                                Certificado Digital (.p12)
                            </h6>
                        </div>

                        <div class="text-center py-3">
                            <div class="rounded-circle bg-warning-subtle text-warning-emphasis d-inline-flex align-items-center justify-content-center p-3 mb-3" style="width: 70px; height: 70px;">
                                <i class="fa-solid fa-shield-halved fs-2"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark">Certificado de Firma Digital</h6>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 small fw-semibold">
                                <i class="fa-solid fa-check me-1"></i> Activo & Válido
                            </span>
                        </div>

                        <ul class="list-group list-group-flush small mt-2">
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="text-muted">Emisor:</span>
                                <span class="fw-semibold text-dark">GSE Certicámara S.A.</span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="text-muted">Titular / NIT:</span>
                                <span class="fw-semibold text-dark">{{ $empresa->nit ?: '901.234.567-8' }}</span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="text-muted">Fecha Vencimiento:</span>
                                <span class="fw-bold text-success">30 Nov 2027</span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="text-muted">Algoritmo:</span>
                                <span class="badge bg-light text-secondary border">SHA256withRSA</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 3: Tipos de Documento DIAN --}}
        <div class="tab-pane fade" id="tab-documentos" role="tabpanel">
            <div class="row g-3">
                {{-- Documento 1: Factura Electrónica --}}
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge bg-primary text-white rounded-pill px-3 py-1 small fw-bold">Tipo 01</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">Habilitado</span>
                        </div>
                        <h6 class="fw-bold mb-1 text-dark">Factura Electrónica de Venta</h6>
                        <p class="text-muted small mb-3">Comprobante principal para transacciones comerciales con validación previa.</p>
                        <div class="mt-auto border-top pt-2 d-flex justify-content-between text-muted small">
                            <span>Prefijo sugerido:</span>
                            <strong class="text-dark">FE</strong>
                        </div>
                    </div>
                </div>

                {{-- Documento 2: Nota Crédito --}}
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge bg-danger text-white rounded-pill px-3 py-1 small fw-bold">Tipo 91</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">Habilitado</span>
                        </div>
                        <h6 class="fw-bold mb-1 text-dark">Nota Crédito Electrónica</h6>
                        <p class="text-muted small mb-3">Documento para anulación, descuento o devolución sobre facturas emitidas.</p>
                        <div class="mt-auto border-top pt-2 d-flex justify-content-between text-muted small">
                            <span>Prefijo sugerido:</span>
                            <strong class="text-dark">NC</strong>
                        </div>
                    </div>
                </div>

                {{-- Documento 3: Nota Débito --}}
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge bg-info text-white rounded-pill px-3 py-1 small fw-bold">Tipo 92</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">Habilitado</span>
                        </div>
                        <h6 class="fw-bold mb-1 text-dark">Nota Débito Electrónica</h6>
                        <p class="text-muted small mb-3">Documento para cobro de intereses o valores adicionales sobre facturas.</p>
                        <div class="mt-auto border-top pt-2 d-flex justify-content-between text-muted small">
                            <span>Prefijo sugerido:</span>
                            <strong class="text-dark">ND</strong>
                        </div>
                    </div>
                </div>

                {{-- Documento 4: Factura POS --}}
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1 small fw-bold">POS</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">Habilitado</span>
                        </div>
                        <h6 class="fw-bold mb-1 text-dark">Documento Equivalente POS</h6>
                        <p class="text-muted small mb-3">Sistema de punto de venta rápido para ventas a mostrador con transmisión DIAN.</p>
                        <div class="mt-auto border-top pt-2 d-flex justify-content-between text-muted small">
                            <span>Prefijo sugerido:</span>
                            <strong class="text-dark">POS</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

{{-- =====================================================
     MODAL CREAR RESOLUCIÓN DIAN
     ===================================================== --}}
<div class="modal fade" id="modalCrearResolucion" tabindex="-1" aria-labelledby="modalCrearResolucionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">

            <div class="modal-header border-bottom px-4 py-3 bg-light rounded-top-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-2 bg-primary text-white d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="fa-solid fa-stamp"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-dark" id="modalCrearResolucionLabel">Nueva Resolución DIAN</h5>
                        <p class="text-muted small mb-0">Registra una autorización oficial de numeración expedida por la DIAN.</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form action="{{ route('parametrizacion.store') }}" method="POST">
                @csrf

                <div class="modal-body px-4 py-3">

                    <div class="alert alert-info border-0 rounded-3 small d-flex align-items-center gap-2 py-2 mb-3">
                        <i class="fa-solid fa-circle-info fs-5"></i>
                        <span>Los datos deben coincidir exactamente con el formulario 1876 descargado del portal MUISCA de la DIAN.</span>
                    </div>

                    <div class="row g-3">
                        {{-- Número de Resolución --}}
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Número de Resolución DIAN *</label>
                            <input type="text"
                                   name="numero_resolucion"
                                   class="form-control"
                                   placeholder="Ej. 18764000001"
                                   value="{{ old('numero_resolucion') }}"
                                   required>
                        </div>

                        {{-- Prefijo --}}
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Prefijo Autorizado *</label>
                            <input type="text"
                                   name="prefijo"
                                   class="form-control text-uppercase"
                                   placeholder="Ej. FE, SETP, POS"
                                   value="{{ old('prefijo') }}"
                                   maxlength="20"
                                   required>
                        </div>

                        {{-- Rango Inicial --}}
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Rango Inicial (Desde) *</label>
                            <input type="number"
                                   name="rango_inicial"
                                   class="form-control"
                                   placeholder="Ej. 1"
                                   value="{{ old('rango_inicial', 1) }}"
                                   min="1"
                                   required>
                        </div>

                        {{-- Rango Final --}}
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Rango Final (Hasta) *</label>
                            <input type="number"
                                   name="rango_final"
                                   class="form-control"
                                   placeholder="Ej. 10000"
                                   value="{{ old('rango_final') }}"
                                   min="1"
                                   required>
                        </div>

                        {{-- Fecha de Vigencia --}}
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Fecha Límite de Vigencia *</label>
                            <input type="date"
                                   name="fecha_vigencia"
                                   class="form-control"
                                   value="{{ old('fecha_vigencia') }}"
                                   required>
                        </div>

                        {{-- Consecutivo Actual --}}
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Consecutivo Actual / Contador *</label>
                            <input type="number"
                                   name="contador_actual"
                                   class="form-control"
                                   placeholder="Ej. 0"
                                   value="{{ old('contador_actual', 0) }}"
                                   min="0"
                                   required>
                            <div class="form-text small text-muted">Último número facturado (0 para iniciar desde el rango inicial).</div>
                        </div>

                        {{-- Estado --}}
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Estado de la Resolución *</label>
                            <select name="estado" class="form-select" required>
                                <option value="activa" @selected(old('estado', 'activa') === 'activa')>Activa (Habilitada para emitir)</option>
                                <option value="inactiva" @selected(old('estado') === 'inactiva')>Inactiva (En reserva)</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="modal-footer border-top px-4 py-3 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary-gradient px-4">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Guardar Resolución
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- =====================================================
     MODAL CONFIGURACIÓN TÉCNICA (INFORMATIVO)
     ===================================================== --}}
<div class="modal fade" id="modalConfigTecnica" tabindex="-1" aria-labelledby="modalConfigTecnicaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">

            <div class="modal-header border-bottom px-4 py-3 bg-light rounded-top-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-2 bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="fa-solid fa-sliders"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-dark" id="modalConfigTecnicaLabel">Parámetros Técnicos del Software DIAN</h5>
                        <p class="text-muted small mb-0">Credenciales de integración con el catálogo de participantes DIAN.</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body px-4 py-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Software ID (Identificador de Software)</label>
                        <input type="text" class="form-control" value="846c4f92-5db1-4cfa-bb01-209e99cfa110" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">PIN del Software</label>
                        <input type="password" class="form-control" value="12345" readonly>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold">Clave Técnica DIAN</label>
                        <input type="text" class="form-control" value="fc8eac422eba16e22ffd8c6f94b3f40a6e38162c" readonly>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold">TestSetId (Set de Pruebas de Habilitación)</label>
                        <input type="text" class="form-control" value="b2c86e09-6c84-48f1-9d56-787db12f60a9" readonly>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold">Ambiente</label>
                        <select class="form-select" disabled>
                            <option value="1" selected>1 - Producción</option>
                            <option value="2">2 - Habilitación / Pruebas</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top px-4 py-3 bg-light rounded-bottom-4">
                <button type="button" class="btn btn-secondary px-4 rounded-3" data-bs-dismiss="modal">
                    Entendido
                </button>
            </div>

        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    // Mostrar u ocultar contraseña/clave técnica
    function toggleVerPassword(inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            if (icon) {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        } else {
            input.type = 'password';
            if (icon) {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    }

    // Confirmación para eliminar resolución
    function confirmarEliminarResolucion(id, nombre) {
        Swal.fire({
            title: '¿Eliminar resolución?',
            text: `La resolución "${nombre}" será eliminada del sistema. Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((resultado) => {
            if (resultado.isConfirmed) {
                document.getElementById('formEliminarResolucion' + id).submit();
            }
        });
    }

    // Alertas de sesión con SweetAlert2
    document.addEventListener('DOMContentLoaded', function () {
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Operación exitosa!',
                text: {{ Illuminate\Support\Js::from(session('success')) }},
                confirmButtonColor: '#ea580c',
                confirmButtonText: 'Aceptar'
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'No se pudo completar',
                text: {{ Illuminate\Support\Js::from(session('error')) }},
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Entendido'
            });
        @endif
    });
</script>
@endsection