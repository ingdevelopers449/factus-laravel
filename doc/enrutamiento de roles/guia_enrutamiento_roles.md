# Guía Definitiva de Enrutamiento y Control de Acceso por Roles (RBAC) en Laravel

Esta guía documenta paso a paso, desde cero y sin omitir ningún detalle, la arquitectura y metodología implementada en **FactuWeb PRO** para el control de acceso y enrutamiento basado en roles en Laravel.

---

## Índice
1. [Arquitectura y Principios de Diseño](#1-arquitectura-y-principios-de-diseño)
2. [Paso 1: Modelos y Migraciones de Base de Datos](#paso-1-modelos-y-migraciones-de-base-de-datos)
3. [Paso 2: Seeders (Población de Roles y Usuarios Iniciales)](#paso-2-seeders-población-de-roles-y-usuarios-iniciales)
4. [Paso 3: Definición de Relaciones en Modelos Eloquent](#paso-3-definición-de-relaciones-en-modelos-eloquent)
5. [Paso 4: Creación y Registro del Middleware de Roles](#paso-4-creación-y-registro-del-middleware-de-roles)
6. [Paso 5: Creación de Controladores por Módulo y Rol](#paso-5-creación-de-controladores-por-módulo-y-rol)
7. [Paso 6: Definición y Protección de Rutas en `routes/web.php`](#paso-6-definición-y-protección-de-rutas-en-routeswebphp)
8. [Paso 7: Renderizado Condicional y Navegación en Vistas Blade](#paso-7-renderizado-condicional-y-navegación-en-vistas-blade)
9. [Paso 8: Errores Frecuentes y Cómo Solucionarlos](#paso-8-errores-frecuentes-y-cómo-solucionarlos)
10. [Resumen de Comandos Artisan (Cheat Sheet)](#resumen-de-comandos-artisan-cheat-sheet)

---

## 1. Arquitectura y Principios de Diseño

### 1.1. Uso de la tabla nativa `users`
Laravel incluye nativamente una tabla `users` optimizada para el sistema de autenticación (Breeze, Jetstream, Fortify o Auth manual), restablecimiento de contraseñas, sesiones y tokens.
- **Regla fundamental:** Nunca crear una tabla redundante como `usuarios`. Todas las cuentas del sistema (Administradores, Vendedores, Cajeros, Empleados) residen en la tabla `users`.
- Cada registro en `users` se vincula a un rol mediante la clave foránea `role_id`.

### 1.2. Tabla de Roles independiente (`roles`)
La tabla `roles` define los perfiles de usuario. Posee únicamente los campos necesarios:
- `id`: Identificador numérico primario.
- `code`: Código unívoco del rol en mayúsculas o minúsculas sin espacios (ejemplo: `'ADMIN'`, `'EMPLEADO'`). Es el valor consultado en middlewares y condicionales.
- `name`: Nombre descriptivo legible para la interfaz gráfica (ejemplo: `'Administrador'`, `'Empleado'`).
- **Regla fundamental:** Evitar columnas redundantes como `nombre_rol` si ya existe `name`.

---

## Paso 1: Modelos y Migraciones de Base de Datos

### 1.1. Crear el Modelo y Migración de Roles
Ejecuta en consola:
```bash
php artisan make:model Role -m
```
*El parámetro `-m` crea automáticamente el archivo de migración en `database/migrations/`.*

Abre la migración generada (`xxxx_xx_xx_xxxxxx_create_roles_table.php`) y define la estructura:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
```

---

### 1.2. Añadir la Columna `role_id` a la Tabla `users`
Para no modificar la migración inicial de Laravel y respetar el historial del proyecto, se crea una migración específica:
```bash
php artisan make:migration add_role_id_to_users_table --table=users
```

Abre el archivo generado en `database/migrations/` y define la clave foránea:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')
                ->nullable()
                ->after('id')
                ->constrained('roles')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
        });
    }
};
```

---

### 1.3. Ejecutar las Migraciones
Aplica los cambios en tu base de datos:
```bash
php artisan migrate
```

---

## Paso 2: Seeders (Población de Roles y Usuarios Iniciales)

### 2.1. Crear el Seeder de Roles
Ejecuta:
```bash
php artisan make:seeder RoleSeeder
```

Edita `database/seeders/RoleSeeder.php` utilizando `upsert` para evitar duplicados en ejecuciones repetidas:
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->upsert(
            [
                ['id' => 1, 'code' => 'ADMIN', 'name' => 'Administrador'],
                ['id' => 2, 'code' => 'EMPLEADO', 'name' => 'Empleado'],
            ],
            ['code'],
            ['name']
        );
    }
}
```

---

### 2.2. Ejecutar el Seeder
```bash
php artisan db:seed --class=RoleSeeder
```

*(Opcional: Si deseas registrar roles por defecto en `database/seeders/DatabaseSeeder.php`, agrega `$this->call(RoleSeeder::class);`).*

---

## Paso 3: Definición de Relaciones en Modelos Eloquent

### 3.1. Modelo `Role.php` (`app/Models/Role.php`)
Un rol tiene muchos usuarios (`HasMany`):
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'code',
        'name',
    ];

    /**
     * Un rol tiene muchos usuarios vinculados.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
```

---

### 3.2. Modelo `User.php` (`app/Models/User.php`)
Un usuario pertenece a un rol (`BelongsTo`). Añade `role_id` a `$fillable` y define la relación:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * El usuario pertenece a un rol.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Método auxiliar opcional para validar rol rápidamente.
     */
    public function hasRole(string $code): bool
    {
        return strtoupper($this->role?->code ?? '') === strtoupper($code);
    }
}
```

---

## Paso 4: Creación y Registro del Middleware de Roles

El middleware intercepta las peticiones HTTP antes de que lleguen al controlador, verificando si el usuario está autenticado y si cuenta con el rol requerido.

### 4.1. Crear el Middleware
Ejecuta:
```bash
php artisan make:middleware EnsureUserHasRole
```

---

### 4.2. Implementar la Lógica del Middleware (`app/Http/Middleware/EnsureUserHasRole.php`)
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Maneja la petición entrante.
     * Permite recibir uno o varios roles separados por coma.
     * Ejemplo: EnsureUserHasRole:ADMIN o EnsureUserHasRole:ADMIN,SUPERVISOR
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // 1. Si no hay usuario en sesión, redirigir al login
        if (!$user) {
            return redirect()->route('login');
        }

        // 2. Obtener el código de rol en mayúsculas
        $userRole = strtoupper($user->role?->code ?? '');

        // 3. Normalizar la lista de roles permitidos a mayúsculas
        $allowedRoles = array_map('strtoupper', $roles);

        // 4. Si el rol del usuario no está permitido, abortar con error 403 Forbidden
        if (!in_array($userRole, $allowedRoles, true)) {
            abort(403, 'Acceso denegado: No cuentas con los permisos requeridos.');
        }

        return $next($request);
    }
}
```

---

### 4.3. Registrar el Alias del Middleware en `bootstrap/app.php` (Laravel 11 / 12 / 13)
Abre `bootstrap/app.php` y registra el alias dentro de `withMiddleware`:
```php
<?php

use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias de middleware para usar en rutas: 'role:ADMIN'
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

---

## Paso 5: Creación de Controladores por Módulo y Rol

Es una excelente práctica separar los controladores en subcarpetas según el rol para evitar colisiones y mantener el código ordenado:
- `app/Http/Controllers/Admin/`
- `app/Http/Controllers/Empleado/`

### 5.1. Comandos Artisan
```bash
# Dashboard para Administrador
php artisan make:controller Admin/DashboardController

# Gestión de Empleados / Usuarios (Controlador con recursos)
php artisan make:controller Admin/UsuarioController --resource

# Dashboard para Empleado
php artisan make:controller Empleado/DashboardController
```

---

### 5.2. Ejemplo de Controlador: `UsuarioController.php`
```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    /**
     * Muestra la lista paginada de usuarios con su rol cargado (Eager Loading).
     */
    public function index(): View
    {
        $usuarios = User::with('role')
            ->orderBy('name')
            ->paginate(10);

        return view('Admin.usuario.index', compact('usuarios'));
    }

    public function create(): View
    {
        $roles = Role::orderBy('name')->get();
        return view('Admin.usuario.create', compact('roles'));
    }

    // Métodos store, edit, update, destroy...
}
```
> **Nota crítica:** No olvides colocar `use App\Models\User;` y `use Illuminate\View\View;` en la cabecera del archivo para evitar el error `Class not found`.

---

## Paso 6: Definición y Protección de Rutas en `routes/web.php`

En `routes/web.php`, se configuran tres elementos clave:
1. **Ruta `/dashboard` central:** Detecta el rol del usuario autenticado y lo redirige automáticamente a su panel correspondiente.
2. **Grupo protegido de Administrador:** Protegido con `auth` y `role:ADMIN`, con prefijo URL `/admin` y prefijo de nombres `admin.`.
3. **Grupo protegido de Empleado:** Protegido con `auth` y `role:EMPLEADO`, con prefijo URL `/empleado` y prefijo de nombres `empleado.`.

### Código Completo y Explicado de `routes/web.php`:
```php
<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Empleado\DashboardController as EmpleadoDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

// 1. Página de inicio pública
Route::get('/', function () {
    return view('welcome');
});

// 2. Ruta central /dashboard: Redirige según el rol del usuario
Route::get('/dashboard', function () {
    return match (strtoupper(auth()->user()?->role?->code ?? '')) {
        'ADMIN'    => redirect()->route('admin.dashboard'),
        'EMPLEADO' => redirect()->route('empleado.dashboard'),
        default    => redirect()->route('login'),
    };
})->middleware(Authenticate::class)->name('dashboard');

// 3. GRUPO ADMINISTRADOR: Acceso exclusivo para rol ADMIN
// URL resultante: /admin/...
// Nombre de ruta resultante: admin....
Route::middleware([Authenticate::class, 'role:ADMIN'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        
        // URL: /admin/dashboard | Ruta: admin.dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        // URL: /admin/usuario | Ruta: admin.usuario.index
        Route::get('/usuario', [UsuarioController::class, 'index'])
            ->name('usuario.index');

        // Si usas Resource Controller:
        // Route::resource('usuario', UsuarioController::class);
    });

// 4. GRUPO EMPLEADO: Acceso exclusivo para rol EMPLEADO
// URL resultante: /empleado/...
// Nombre de ruta resultante: empleado....
Route::middleware([Authenticate::class, 'role:EMPLEADO'])
    ->prefix('empleado')
    ->name('empleado.')
    ->group(function () {
        
        // URL: /empleado/dashboard | Ruta: empleado.dashboard
        Route::get('/dashboard', [EmpleadoDashboardController::class, 'index'])
            ->name('dashboard');
    });

// 5. Rutas comunes para usuarios autenticados (Perfil)
Route::middleware(Authenticate::class)->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
```

---

## Paso 7: Renderizado Condicional y Navegación en Vistas Blade

Para que la interfaz gráfica muestre u oculte menús dependiendo de los privilegios del usuario:

### 7.1. Condicional de Rol en Blade
```blade
{{-- Opción visible únicamente para Administradores --}}
@if (strtoupper(auth()->user()->role?->code ?? '') === 'ADMIN')
    <li class="nav-item">
        <a href="{{ route('admin.usuario.index') }}" 
           class="nav-link {{ request()->routeIs('admin.usuario.*') ? 'active' : '' }}">
            <i class="fa-solid fa-user-shield"></i> Empleados
        </a>
    </li>
@endif
```

### 7.2. Resaltado Activo en Menús
La función `request()->routeIs('admin.usuario.*')` verifica si la URL actual coincide con cualquier acción del recurso `usuario`, asignando automáticamente la clase CSS `active` al enlace seleccionado.

---

## Paso 8: Errores Frecuentes y Cómo Solucionarlos

| Error | Causa | Solución |
|---|---|---|
| `Class "App\Http\Controllers\Admin\User" not found` | No se importó el modelo `User` en la parte superior del controlador. | Añadir `use App\Models\User;` al inicio del archivo. |
| `Invalid route action: [UsuarioController]` | En `routes/web.php` se pasaron los argumentos separados por comas: `Route::get('/ruta', Controller::class, 'metodo');`. | Usar sintaxis de arreglo: `Route::get('/ruta', [Controller::class, 'metodo']);`. |
| URL duplicada: `/admin/admin/usuario` | El grupo ya tiene `->prefix('admin')` y en la ruta hija se volvió a escribir `/admin/usuario`. | Dejar la ruta hija como `Route::get('/usuario', ...);`. |
| `403 This action is unauthorized` o menú invisible | Discrepancia entre mayúsculas y minúsculas en el código del rol (ejemplo: en BD dice `'admin'` y en el código se compara con `'ADMIN'`). | Normalizar siempre con `strtoupper($role->code ?? '') === 'ADMIN'`. |
| Relación circular o error de método en `Role.php` | Se definió accidentalmente `public function role(): BelongsTo` dentro de `Role.php`. | En `Role.php` solo va `users(): HasMany`. La relación `role(): BelongsTo` va en `User.php`. |

---

## Resumen de Comandos Artisan (Cheat Sheet)

| Acción | Comando |
|---|---|
| Crear modelo y migración de Roles | `php artisan make:model Role -m` |
| Crear migración para enlazar `role_id` a `users` | `php artisan make:migration add_role_id_to_users_table --table=users` |
| Ejecutar migraciones | `php artisan migrate` |
| Crear seeder de Roles | `php artisan make:seeder RoleSeeder` |
| Ejecutar seeder de Roles | `php artisan db:seed --class=RoleSeeder` |
| Crear middleware de Roles | `php artisan make:middleware EnsureUserHasRole` |
| Crear controlador de Administrador | `php artisan make:controller Admin/DashboardController` |
| Crear controlador de Recursos | `php artisan make:controller Admin/UsuarioController --resource` |
| Crear controlador de Empleado | `php artisan make:controller Empleado/DashboardController` |
| Listar y verificar rutas registradas | `php artisan route:list` |
| Aplicar formato de código (Pint) | `vendor/bin/pint --dirty --format agent` |
