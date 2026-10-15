<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - FactuWeb PRO</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- FontAwesome 6.5.0 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/favicon.svg') }}">

    <!-- Vite Styles (app.css) & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    <div class="auth-wrapper">
        <div class="auth-form-container">
            <!-- Header -->
            <div class="text-center">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('img/logo_nuevo.svg') }}" alt="FactuWeb PRO" class="auth-logo">
                </a>
                <h2 class="auth-title">¡Bienvenido de nuevo!</h2>
                <p class="auth-subtitle">Ingresa tus credenciales para acceder al sistema</p>
            </div>

            <!-- Estado de la sesión (mensajes de éxito/notificación) -->
            @if (session('status'))
                <div class="alert alert-success bg-success bg-opacity-25 text-white border-0 py-2 px-3 mb-3 small" role="alert">
                    <i class="fa-solid fa-circle-check me-1"></i> {{ session('status') }}
                </div>
            @endif

            <form id="loginForm" action="{{ route('login') }}" method="POST">
                @csrf
                
                <!-- Email -->
                <div class="mb-4">
                    <label class="form-label form-label-dark" for="email">Correo Electrónico</label>
                    <div class="input-dark-wrapper">
                        <div class="input-dark-icon">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <input type="email" name="email" id="email" class="input-dark-field" placeholder="nombre@ejemplo.com" value="{{ old('email') }}" required autofocus autocomplete="username">
                    </div>
                    @error('email')
                        <span class="text-danger small mt-1 d-block">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label class="form-label form-label-dark" for="password_hash">
                        <span>Contraseña</span>
                    </label>
                    <div class="input-dark-wrapper position-relative">
                        <div class="input-dark-icon">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input type="password" name="password" id="password_hash" class="input-dark-field pe-5" placeholder="••••••••" required autocomplete="current-password">
                        <button type="button" class="btn position-absolute end-0 top-50 translate-middle-y border-0 text-secondary p-2 me-1" id="togglePassword" tabindex="-1">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="text-danger small mt-1 d-block">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                <!-- Checkbox -->
                <div class="form-check mb-4 mt-3">
                    <input class="form-check-input bg-dark border-secondary shadow-none" type="checkbox" name="remember" id="rememberMe">
                    <label class="form-check-label check-dark-label" for="rememberMe">
                        Recordarme en este dispositivo
                    </label>
                </div>

                <!-- Botón Ingresar -->
                <button type="submit" class="btn btn-auth-submit w-100 mb-3 mt-2">
                    Iniciar Sesión <i class="fa-solid fa-arrow-right-to-bracket ms-2"></i>
                </button>
            </form>

            <!-- Link recuperación FUERA del form para evitar validación accidental -->
            @if (Route::has('password.request'))
                <div class="text-center mt-2">
                    <a href="{{ route('password.request') }}" class="text-decoration-none" style="color: rgba(255,255,255,0.55); font-size: 0.85rem;">
                        <i class="fa-solid fa-key me-1"></i> ¿Olvidaste tu contraseña?
                    </a>
                </div>
            @endif

            <!-- Link a registro si está disponible -->
            @if (Route::has('register'))
                <div class="text-center mt-3 pt-3 border-top border-secondary border-opacity-25">
                    <span style="color: rgba(255,255,255,0.6); font-size: 0.85rem;">¿No tienes una cuenta? </span>
                    <a href="{{ route('register') }}" class="text-decoration-none fw-semibold" style="color: #f59e0b; font-size: 0.85rem;">
                        Registrarse
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script>
        // Toggle Password Visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password_hash');

        if(togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        }

        // Prevenir múltiples clics al iniciar sesión (Problema de concurrencia)
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', function() {
                if (!this.checkValidity()) {
                    return;
                }
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin me-2"></i> Procesando...';
                }
            });
        }
    </script>

    <!-- SweetAlert Global si existe sesión de alerta -->
    @if (session('alert'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: '{{ session('alert.icon', 'info') }}',
                    title: '{{ session('alert.title', '') }}',
                    text: '{{ session('alert.text', '') }}',
                    background: '#1f2937',
                    color: '#fff',
                    confirmButtonColor: '#f59e0b'
                });
            });
        </script>
    @endif

</body>
</html>
