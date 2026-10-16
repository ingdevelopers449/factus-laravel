# Documentación Técnica: Modal de Creación de Nuevos Empleados (FactuWeb PRO)

Este documento detalla paso a paso la arquitectura, el flujo de datos, el enrutamiento, la validación y la interfaz de usuario implementada para el **Modal de Registro de Nuevos Empleados / Usuarios** en FactuWeb PRO.

---

## Índice
1. [Flujo General del Sistema](#1-flujo-general-del-sistema)
2. [El Problema Inicial y su Resolución (`$roles`)](#2-el-problema-inicial-y-su-resolución-roles)
3. [Paso 1: Controlador `UsuarioController.php`](#paso-1-controlador-usuariocontrollerphp)
4. [Paso 2: Enrutamiento en `routes/web.php`](#paso-2-enrutamiento-en-routeswebphp)
5. [Paso 3: Estructura de la Vista e Interfaz Blade](#paso-3-estructura-de-la-vista-e-interfaz-blade)
6. [Paso 4: Seguridad y Hashing en el Modelo `User.php`](#paso-4-seguridad-y-hashing-en-el-modelo-userphp)
7. [Catálogo de Errores Frecuentes y Prevención](#catálogo-de-errores-frecuentes-y-prevención)
8. [Checklist de Verificación](#checklist-de-verificación)

---

## 1. Flujo General del Sistema

El proceso de creación de empleados sigue el patrón estándar de arquitectura MVC en Laravel:

```
[ Vista: admin/usuario/index ]
        │
        ├──► 1. Clic en botón "Nuevo Empleado" (Abre modal Bootstrap #modalNuevoEmpleado)
        │
        ├──► 2. Formulario carga select dinámico con la lista de $roles
        │
        ├──► 3. Usuario completa datos y presiona "Guardar empleado"
        │
[ Petición POST a /admin/usuario ]
        │
        ├──► 4. Middlewares verifican autenticación ('auth') y rol ('role:admin')
        │
[ Controlador: UsuarioController@store ]
        │
        ├──► 5. Valida campos (name, email único, role_id existente, password confirmado)
        │
        ├──► 6. Inserta registro en la tabla 'users' (password se hashea automáticamente)
        │
        └──► 7. Redirige a admin.usuario.index con mensaje Flash session('success')
                │
                ▼
[ Vista: Muestra alerta verde y el nuevo usuario en la tabla ]
```

---

## 2. El Problema Inicial y su Resolución (`$roles`)

### El Error Detectado
Al agregar el modal dentro de `resources/views/admin/usuario/index.blade.php`, se insertó un elemento `<select>` con un bucle:
```blade
@foreach ($roles as $rol)
    <option value="{{ $rol->id }}">{{ $rol->name }}</option>
@endforeach
```
Al cargar la página, Laravel arrojó la excepción:
```text
Undefined variable $roles
```

### Causa Raíz
El método `index()` en `UsuarioController.php` únicamente consultaba la tabla de usuarios y enviaba esa variable a la vista:
```php
// ANTES (Causaba el error)
public function index(): View
{
    $usuarios = User::with('role')->orderBy('name')->paginate(10);
    return view('admin.usuario.index', compact('usuarios'));
}
```

### Solución Definitiva
Se importó el modelo `Role` y se consultaron todos los roles ordenados por nombre para enviarlos junto a `$usuarios`:
```php
// DESPUÉS (Corregido)
use App\Models\Role; // <-- Importación obligatoria

public function index(): View
{
    $usuarios = User::with('role')->orderBy('name')->paginate(10);
    $roles = Role::orderBy('name')->get(); // <-- Consulta de roles

    return view('admin.usuario.index', compact('usuarios', 'roles'));
}
```

---

## Paso 1: Controlador `UsuarioController.php`

El controlador gestiona tanto el listado inicial como el procesamiento del formulario `POST`.

**Archivo:** `app/Http/Controllers/Admin/UsuarioController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    /**
     * Muestra la lista de usuarios y provee los roles para el modal.
     */
    public function index(): View
    {
        $usuarios = User::with('role')
            ->orderBy('name')
            ->paginate(10);

        $roles = Role::orderBy('name')->get();

        return view('admin.usuario.index', compact('usuarios', 'roles'));
    }

    /**
     * Almacena un nuevo empleado en la base de datos tras validar los datos.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. VALIDACIÓN ESTRICTA DE LOS DATOS
        $datos = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'role_id'  => ['required', 'exists:roles,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // 2. CREACIÓN DEL USUARIO EN LA TABLA NATIVA 'users'
        User::create($datos);

        // 3. RETORNO CON MENSAJE FLASH DE ÉXITO
        return redirect()
            ->route('admin.usuario.index')
            ->with('success', 'Empleado registrado correctamente');
    }
}
```

### Desglose de las Reglas de Validación:
| Campo | Reglas | Explicación Técnica |
|---|---|---|
| `name` | `required`, `string`, `max:255` | El nombre es obligatorio, debe ser texto y no superar 255 caracteres. |
| `email` | `required`, `email`, `max:255`, `unique:users,email` | Debe ser un email válido y **no existir previamente** en la columna `email` de la tabla `users` (evita duplicidad). |
| `role_id` | `required`, `exists:roles,id` | Es obligatorio y el ID seleccionado **debe existir** en la columna `id` de la tabla `roles` (integridad referencial). |
| `password` | `required`, `string`, `min:8`, `confirmed` | Mínimo 8 caracteres. La regla `confirmed` exige que exista en la petición un campo llamado exactamente `password_confirmation` con el mismo valor. |

---

## Paso 2: Enrutamiento en `routes/web.php`

Para que el formulario pueda enviar la petición `POST`, se declaró la ruta dentro del grupo protegido del Administrador.

**Archivo:** `routes/web.php`

```php
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

Route::middleware([Authenticate::class, EnsureUserHasRole::class.':admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        
        // RUTA GET: Muestra la vista con la tabla y el modal
        Route::get('/usuario', [UsuarioController::class, 'index'])
            ->name('usuario.index');

        // RUTA POST: Procesa el formulario del modal
        Route::post('/admin/usuario', [UsuarioController::class, 'store'])
            ->name('usuario.store');
    });
```

### Detalles Clave del Enrutamiento:
1. **Protección:** Tanto el acceso a la vista (`GET`) como el guardado (`POST`) están blindados por `Authenticate` (sesión activa) y `EnsureUserHasRole:admin` (solo rol administrador).
2. **Nombre de Ruta (`name`):** Al estar dentro del grupo con `->name('admin.')`, la ruta se resuelve en Blade mediante:
   ```blade
   {{ route('admin.usuario.store') }}
   ```

---

## Paso 3: Estructura de la Vista e Interfaz Blade

**Archivo:** `resources/views/admin/usuario/index.blade.php`

La vista contiene 4 componentes esenciales:

### 3.1. Alertas de Éxito y Errores (Flash Messages)
Colocadas en la parte superior del contenido para notificar al usuario el resultado de su acción:

```blade
{{-- Mensaje de Éxito al Crear Usuario --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif

{{-- Lista de Errores de Validación (si falló algún campo) --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong><i class="fa-solid fa-triangle-exclamation me-2"></i>Revisa los siguientes errores:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif
```

---

### 3.2. Botón Disparador del Modal
Ubicado en la cabecera de la sección. Utiliza los atributos nativos de Bootstrap 5 para abrir el modal sin necesidad de escribir código JavaScript personalizado:

```blade
<button type="button"
        class="btn btn-primary-gradient d-inline-flex align-items-center gap-2 px-3 py-2 shadow-sm"
        data-bs-toggle="modal"
        data-bs-target="#modalNuevoEmpleado">
    <i class="fa-solid fa-user-plus"></i>
    <span>Nuevo Empleado</span>
</button>
```
- `data-bs-toggle="modal"`: Indica a Bootstrap que este botón activa una ventana modal.
- `data-bs-target="#modalNuevoEmpleado"`: Apunta al ID exacto del modal en el DOM.

---

### 3.3. Estructura Completa del Modal
Ubicado al final de la vista:

```blade
<!-- Modal: Nuevo Empleado -->
<div class="modal fade" id="modalNuevoEmpleado" tabindex="-1" aria-labelledby="tituloModalNuevoEmpleado" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">

            <!-- Encabezado del Modal -->
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo del Modal con Formulario -->
            <div class="modal-body p-4">
                <form id="formNuevoEmpleado" action="{{ route('admin.usuario.store') }}" method="POST">
                    @csrf {{-- Token de seguridad obligatorio contra ataques Cross-Site Request Forgery --}}

                    <!-- Nombre completo -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Nombre completo</label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               placeholder="Ej. Juan Pérez" 
                               required>
                    </div>

                    <!-- Correo electrónico -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Correo electrónico</label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               placeholder="usuario@correo.com" 
                               required>
                    </div>

                    <!-- Rol del usuario (Select Dinámico) -->
                    <div class="mb-3">
                        <label for="role_id" class="form-label fw-semibold">Rol del usuario</label>
                        <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
                            <option value="">Selecciona un rol</option>
                            @foreach ($roles as $rol)
                                <option value="{{ $rol->id }}" {{ old('role_id') == $rol->id ? 'selected' : '' }}>
                                    {{ $rol->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Contraseña -->
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Contraseña</label>
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               placeholder="Mínimo 8 caracteres" 
                               required>
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label fw-semibold">Confirmar contraseña</label>
                        <input type="password" 
                               class="form-control" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               placeholder="Repite la contraseña" 
                               required>
                    </div>
                </form>
            </div>

            <!-- Pie del Modal con Acciones -->
            <div class="modal-footer border-top px-4 py-3">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                    Cancelar
                </button>

                <!-- El atributo form='formNuevoEmpleado' vincula el botón al formulario superior -->
                <button type="submit" form="formNuevoEmpleado" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk me-1"></i>
                    Guardar empleado
                </button>
            </div>

        </div>
    </div>
</div>
```

### Características Técnicas del Formulario:
1. **Directiva `@csrf`:** Genera un campo oculto `<input type="hidden" name="_token" value="...">` indispensable para que Laravel no rechace la petición con error `419 Page Expired`.
2. **Atributo HTML5 `form="formNuevoEmpleado"` en el botón de Guardar:** Permite que el botón resida en el `.modal-footer` fuera de la etiqueta `<form>` física, pero dispare el submit del formulario correctamente.
3. **Persistencia con `value="{{ old('nombre_campo') }}"`:** Si la validación falla (ej. contraseña muy corta), los campos completados anteriormente conservan su valor para no tener que escribirlos de nuevo.

---

## Paso 4: Seguridad y Hashing en el Modelo `User.php`

En Laravel 11 / 12 / 13, el hashing de la contraseña se maneja automáticamente mediante Eloquent Casts.

**Archivo:** `app/Models/User.php`

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[Fillable(['name', 'email', 'password', 'role_id'])]
class User extends Authenticatable
{
    /**
     * Define las transformaciones nativas de atributos.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // <-- ¡CRÍTICO! Hashea automáticamente con Bcrypt
        ];
    }
}
```

### ¿Por qué esto es vital?
Al declarar `'password' => 'hashed'` en `casts()`, la llamada:
```php
User::create($datos);
```
toma la contraseña en texto plano validada en `$datos['password']` y la encripta automáticamente con el algoritmo Bcrypt antes de ejecutar el `INSERT INTO users`. **Nunca se guarda una contraseña en texto claro en la base de datos.**

---

## Catálogo de Errores Frecuentes y Prevención

| Error | Causa | Solución |
|---|---|---|
| `Undefined variable $roles` | El método `index()` no envió la colección de roles en la función `view()`. | Ejecutar `$roles = Role::orderBy('name')->get();` y enviarlo con `compact('usuarios', 'roles')`. |
| `419 Page Expired` | Falta la directiva `@csrf` dentro del formulario. | Colocar `@csrf` inmediatamente después de la apertura de la etiqueta `<form>`. |
| `The password confirmation does not match` | El campo de confirmación no se llama `password_confirmation`. | La regla `'confirmed'` exige obligatoriamente que el input secundario tenga `name="password_confirmation"`. |
| El botón "Guardar" no envía nada | El botón está en el footer y no tiene el atributo `form="idDelFormulario"`. | Asegurarse de que el `<form id="formNuevoEmpleado">` tenga el mismo ID que `form="formNuevoEmpleado"` en el botón. |
| `The email has already been taken` | El correo ingresado ya existe en la base de datos. | Comportamiento correcto de la regla `'unique:users,email'`. El usuario debe registrar un correo diferente. |
| Tag `<form>` mal cerrado | Falta el corchete de cierre `>` en la etiqueta `<form>` antes de `@csrf`. | Escribir `<form ... method="POST">` con su `>` correspondiente. |

---

## Checklist de Verificación

Antes de replicar este patrón en otros módulos (ej. Clientes, Productos, Categorías), comprueba esta lista:

- [ ] ¿El método `index()` del controlador consulta y envía a la vista los modelos requeridos para los `<select>` (ej. `$roles`, `$categorias`)?
- [ ] ¿El controlador tiene implementado el método `store(Request $request): RedirectResponse` con validación estricta?
- [ ] ¿El modelo tiene los campos registrados en `$fillable` (o atributo `#[Fillable]`)?
- [ ] ¿La ruta `Route::post(...)` está registrada dentro del grupo protegido por rol en `routes/web.php`?
- [ ] ¿El modal cuenta con un `id` único (ej. `#modalNuevoEmpleado`) que coincide con el `data-bs-target` del botón disparador?
- [ ] ¿El formulario tiene la directiva `@csrf` y `method="POST"`?
- [ ] ¿El botón de envío en el footer tiene el atributo `form="idDelFormulario"`?
- [ ] ¿La vista incluye los bloques `@if (session('success'))` y `@if ($errors->any())` para retroalimentar al usuario?
