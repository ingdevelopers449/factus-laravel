@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Configuración de Empresa - FactuWeb PRO')

@section('content')
<div class="container-fluid p-0">

    {{-- Encabezado de la página --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-building text-primary"></i>
                Datos de la Empresa
            </h4>
            <p class="text-muted small mb-0">
                Gestiona la información fiscal, de contacto y el logo oficial para la emisión de comprobantes y facturas.
            </p>
        </div>

        <div>
            <span class="badge badge-soft-primary px-3 py-2 rounded-pill small fw-semibold">
                <i class="fa-solid fa-shield-halved me-1"></i> Perfil Emisor Principal
            </span>
        </div>
    </div>

    {{-- Alertas de Validación / Sesión --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="fa-solid fa-circle-exclamation text-danger fs-5"></i>
                <strong class="text-danger">Por favor corrige los siguientes errores:</strong>
            </div>
            <ul class="mb-0 ps-3 small text-danger">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    {{-- Formulario Principal --}}
    <form action="{{ route('empresas.update') }}" method="POST" enctype="multipart/form-data" id="formEmpresa">
        @csrf

        <div class="row g-4">

            {{-- Columna Izquierda: Logo y Tarjeta de Presentación --}}
            <div class="col-12 col-lg-4">

                {{-- Card del Logo --}}
                <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                            <i class="fa-solid fa-image text-primary"></i>
                            Logo de la Empresa
                        </h6>
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="empresa-logo-preview-box mb-3 shadow-xs" id="logoPreviewContainer">
                            @if (!empty($empresa?->logo_url))
                                <img src="{{ asset($empresa->logo_url) }}" alt="Logo Empresa" id="imgPreview">
                            @else
                                <div id="logoPlaceholder" class="text-center text-muted">
                                    <i class="fa-solid fa-building-columns fs-1 text-secondary opacity-50 mb-1 d-block"></i>
                                    <span class="small fw-semibold text-secondary">Sin logo</span>
                                </div>
                                <img src="" alt="Previsualización de Logo" id="imgPreview" class="d-none">
                            @endif
                        </div>

                        <p class="text-muted small mb-3">
                            Este logo se imprimirá en los tickets de venta y facturas electrónicas.
                        </p>

                        <label for="inputLogo" class="btn btn-outline-primary btn-sm px-3 rounded-pill w-100 fw-semibold cursor-pointer">
                            <i class="fa-solid fa-upload me-1"></i> Seleccionar Imagen
                        </label>
                        <input type="file"
                               name="logo"
                               id="inputLogo"
                               class="d-none"
                               accept="image/jpeg,image/png,image/jpg,image/webp,image/svg+xml">

                        <div class="form-text small mt-2">
                            Formatos: PNG, JPG, WEBP, SVG (Máx. 2MB).
                        </div>
                    </div>
                </div>

                {{-- Card Preview Tarjeta de Presentación --}}
                <div class="card border-0 shadow-sm rounded-4 bg-white">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                            <i class="fa-solid fa-id-card text-primary"></i>
                            Ficha Rápida
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <span class="text-muted small d-block">Razón Social:</span>
                            <strong class="text-dark" id="previewRazonSocial">
                                {{ $empresa?->razon_social ?? 'No configurada' }}
                            </strong>
                        </div>
                        <div class="mb-3">
                            <span class="text-muted small d-block">NIT / RUT:</span>
                            <span class="badge bg-light text-dark border px-2 py-1 fw-bold" id="previewNit">
                                {{ $empresa?->nit ?? 'Sin NIT' }}
                            </span>
                        </div>
                        <div class="mb-3">
                            <span class="text-muted small d-block">Teléfono:</span>
                            <span class="text-secondary small" id="previewTelefono">
                                {{ $empresa?->telefono ?? 'No registrado' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Email Fiscal:</span>
                            <span class="text-secondary small text-break" id="previewEmail">
                                {{ $empresa?->email ?? 'No registrado' }}
                            </span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Columna Derecha: Formulario de Datos Fiscales y de Contacto --}}
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 bg-white">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                <i class="fa-solid fa-file-invoice text-primary"></i>
                                Información Tributaria y Comercial
                            </h6>
                            <span class="badge bg-light text-muted border small">
                                Campos con (*) son obligatorios
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3">

                            {{-- NIT / Identificación --}}
                            <div class="col-12 col-md-5">
                                <label for="nit" class="form-label fw-semibold text-dark small">
                                    NIT / Documento Tributario <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0">
                                        <i class="fa-solid fa-hashtag"></i>
                                    </span>
                                    <input type="text"
                                           class="form-control bg-light border-start-0 ps-0 @error('nit') is-invalid @enderror"
                                           id="nit"
                                           name="nit"
                                           placeholder="Ej. 900.123.456-7"
                                           value="{{ old('nit', $empresa?->nit) }}"
                                           required>
                                </div>
                                @error('nit')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Razón Social --}}
                            <div class="col-12 col-md-7">
                                <label for="razon_social" class="form-label fw-semibold text-dark small">
                                    Razón Social / Nombre Comercial <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0">
                                        <i class="fa-solid fa-building"></i>
                                    </span>
                                    <input type="text"
                                           class="form-control bg-light border-start-0 ps-0 @error('razon_social') is-invalid @enderror"
                                           id="razon_social"
                                           name="razon_social"
                                           placeholder="Ej. Mi Negocio S.A.S."
                                           value="{{ old('razon_social', $empresa?->razon_social) }}"
                                           required>
                                </div>
                                @error('razon_social')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Correo Electrónico --}}
                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label fw-semibold text-dark small">
                                    Correo Electrónico de Contacto
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0">
                                        <i class="fa-solid fa-envelope"></i>
                                    </span>
                                    <input type="email"
                                           class="form-control bg-light border-start-0 ps-0 @error('email') is-invalid @enderror"
                                           id="email"
                                           name="email"
                                           placeholder="facturacion@miempresa.com"
                                           value="{{ old('email', $empresa?->email) }}">
                                </div>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Teléfono / Celular --}}
                            <div class="col-12 col-md-6">
                                <label for="telefono" class="form-label fw-semibold text-dark small">
                                    Teléfono / PBX / Celular
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0">
                                        <i class="fa-solid fa-phone"></i>
                                    </span>
                                    <input type="text"
                                           class="form-control bg-light border-start-0 ps-0 @error('telefono') is-invalid @enderror"
                                           id="telefono"
                                           name="telefono"
                                           placeholder="Ej. +57 300 123 4567"
                                           value="{{ old('telefono', $empresa?->telefono) }}">
                                </div>
                                @error('telefono')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Dirección Física --}}
                            <div class="col-12">
                                <label for="direccion" class="form-label fw-semibold text-dark small">
                                    Dirección Física del Establecimiento
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </span>
                                    <input type="text"
                                           class="form-control bg-light border-start-0 ps-0 @error('direccion') is-invalid @enderror"
                                           id="direccion"
                                           name="direccion"
                                           placeholder="Ej. Cra. 15 # 85-30, Local 102"
                                           value="{{ old('direccion', $empresa?->direccion) }}">
                                </div>
                                @error('direccion')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Footer de Guardado --}}
                    <div class="card-footer bg-light border-top py-3 px-4 d-flex justify-content-between align-items-center">
                        <span class="text-muted small">
                            <i class="fa-solid fa-circle-info me-1"></i>
                            Los cambios se aplicarán inmediatamente en todo el sistema.
                        </span>

                        <button type="submit" class="btn btn-primary-gradient px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Guardar Configuración</span>
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </form>

</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Notificación SweetAlert2 si hay mensaje de éxito en sesión
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Operación Exitosa!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#3b82f6',
                timer: 3500,
                timerProgressBar: true
            });
        @endif

        // Previsualización interactiva del archivo de logo
        const inputLogo = document.getElementById('inputLogo');
        const imgPreview = document.getElementById('imgPreview');
        const logoPlaceholder = document.getElementById('logoPlaceholder');

        if (inputLogo && imgPreview) {
            inputLogo.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        imgPreview.src = event.target.result;
                        imgPreview.classList.remove('d-none');
                        if (logoPlaceholder) {
                            logoPlaceholder.classList.add('d-none');
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Actualización dinámica de la ficha rápida al tipear
        const inputNit = document.getElementById('nit');
        const inputRazonSocial = document.getElementById('razon_social');
        const inputTelefono = document.getElementById('telefono');
        const inputEmail = document.getElementById('email');

        const previewNit = document.getElementById('previewNit');
        const previewRazonSocial = document.getElementById('previewRazonSocial');
        const previewTelefono = document.getElementById('previewTelefono');
        const previewEmail = document.getElementById('previewEmail');

        if (inputNit && previewNit) {
            inputNit.addEventListener('input', function () {
                previewNit.textContent = this.value.trim() || 'Sin NIT';
            });
        }
        if (inputRazonSocial && previewRazonSocial) {
            inputRazonSocial.addEventListener('input', function () {
                previewRazonSocial.textContent = this.value.trim() || 'No configurada';
            });
        }
        if (inputTelefono && previewTelefono) {
            inputTelefono.addEventListener('input', function () {
                previewTelefono.textContent = this.value.trim() || 'No registrado';
            });
        }
        if (inputEmail && previewEmail) {
            inputEmail.addEventListener('input', function () {
                previewEmail.textContent = this.value.trim() || 'No registrado';
            });
        }
    });
</script>
@endsection