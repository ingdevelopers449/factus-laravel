# Manual Completo: Cómo Enrutar, Proteger y Diseñar Vistas por Roles en Laravel

Este documento es una guía exhaustiva paso a paso para **crear una vista, enrutarla, protegerla por roles de usuario y evitar todos los errores comunes** en Laravel 11 / 12 / 13.

---

## Índice
1. [Las 3 Capas de Seguridad en Laravel](#1-las-3-capas-de-seguridad-en-laravel)
2. [Métodos para Enrutar una Vista](#2-métodos-para-enrutar-una-vista)
3. [Cómo Proteger las Rutas por Rol (Técnicas Oficiales)](#3-cómo-proteger-las-rutas-por-rol-técnicas-oficiales)
4. [Cómo Crear y Conectar la Vista Blade](#4-cómo-crear-y-conectar-la-vista-blade)
5. [Protección Visual en Blade (Menús, Botones y Enlaces Condicionales)](#5-protección-visual-en-blade-menús-botones-y-enlaces-condicionales)
6. [Catálogo de Errores Críticos y Cómo Evitarlos](#6-catálogo-de-errores-críticos-y-cómo-evitarlos)
7. [Paso a Paso Completo con un Ejemplo Real (Módulo "Clientes")](#7-paso-a-paso-completo-con-un-ejemplo-real-módulo-clientes)
8. [Checklist Rápido de Validación](#8-checklist-rápido-de-validación)

---

## 1. Las 3 Capas de Seguridad en Laravel

Cuando construyes una sección restringida en tu sistema (por ejemplo, el panel de administración), la seguridad debe aplicarse en **tres niveles obligatorios**:

```
[ Petición HTTP ] 
       │
       ▼
[ 1. Capa de Ruta / Middleware ]  ──► ¿El usuario tiene sesión activa y el rol requerido?
       │                              Si NO: Aborta con 403 Forbidden o redirige al Login.
       ▼
[ 2. Capa de Controlador ]        ──► Resuelve modelos, valida permisos internos y retorna la vista.
       │                              Si faltan imports: lanza excepción Class Not Found.
       ▼
[ 3. Capa de Vista (Blade) ]      ──► Oculta o muestra botones, tablas y menús según el rol.
```

1. **Capa de Ruta / Middleware:** Impide que un usuario no autorizado acceda escribiendo directamente la URL en la barra del navegador (ejemplo: `http://127.0.0.1:8000/admin/usuario`).
2. **Capa de Controlador:** Encapsula la lógica de negocio, realiza consultas Eloquent con relaciones limpias y garantiza que la respuesta sea una instancia válida de `View`.
3. **Capa de Vista (Blade):** Oculta enlaces del sidebar, botones de creación, edición o eliminación para que el usuario solo vea las opciones que le corresponden.

---

## 2. Métodos para Enrutar una Vista

Existen 4 formas de enrutar vistas en Laravel. Dependiendo del caso de uso, debes elegir la adecuada:

### Método 1: Mediante Controlador (Recomendado para el 95% de los casos)
Se utiliza siempre que la vista necesite consultar bases de datos, listar registros paginados, procesar formularios o aplicar lógica antes de mostrarse.

#### 1. Crear el Controlador con Artisan
```bash
# Para controladores estándar dentro de una subcarpeta
php artisan make:controller Admin/ProductoController

# O si necesitas las 7 acciones CRUD completas (index, create, store, show, edit, update, destroy):
php artisan make:controller Admin/ProductoController --resource
```

#### 2. Escribir el Método en el Controlador
```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto; // <-- Importación obligatoria
use Illuminate\Http\Request;
use Illuminate\View\View; // <-- Importación obligatoria para el tipado : View

class ProductoController extends Controller
{
    /**
     * Muestra la vista principal con los datos paginados.
     */
    public function index(): View
    {
        $productos = Producto::orderBy('nombre')->paginate(10);

        // Retorna la vista pasando los datos
        return view('Admin.producto.index', compact('productos'));
    }
}
```

#### 3. Registrar la Ruta en `routes/web.php`
```php
use App\Http\Controllers\Admin\ProductoController; // <-- Importar arriba

Route::get('/admin/producto', [ProductoController::class, 'index'])
    ->name('admin.producto.index');
```
> **REGLA DE ORO:** El segundo parámetro SIEMPRE debe ser un array con dos elementos: `[NombreControlador::class, 'nombreMetodo']`. **NUNCA** los separes con comas sueltas fuera del array.

---

### Método 2: Enrutamiento directo con `Route::view()`
Útil **únicamente** para páginas informativas estáticas (Términos y Condiciones, Ayuda, Acerca de) que **no** consultan la base de datos.
```php
Route::view('/terminos', 'pages.terminos')->name('terminos');
```

---

### Método 3: Enrutamiento mediante Resource Controller
Si tu controlador implementa todas las operaciones CRUD (crear, leer, actualizar, borrar), `Route::resource` genera las 7 rutas automáticamente en una sola línea:
```php
use App\Http\Controllers\Admin\ProductoController;

Route::resource('producto', ProductoController::class);
```

Rutas generadas automáticamente:
| Método HTTP | URL | Acción del Controlador | Nombre de Ruta |
|---|---|---|---|
| `GET` | `/producto` | `index` | `producto.index` |
| `GET` | `/producto/create` | `create` | `producto.create` |
| `POST` | `/producto` | `store` | `producto.store` |
| `GET` | `/producto/{id}` | `show` | `producto.show` |
| `GET` | `/producto/{id}/edit` | `edit` | `producto.edit` |
| `PUT/PATCH` | `/producto/{id}` | `update` | `producto.update` |
| `DELETE` | `/producto/{id}` | `destroy` | `producto.destroy` |

Si solo deseas generar ciertas acciones:
```php
Route::resource('producto', ProductoController::class)->only(['index', 'show']);
```

---

## 3. Cómo Proteger las Rutas por Rol (Técnicas Oficiales)

### Técnica A: Agrupación con Prefijo y Middleware (La mejor práctica)
En proyectos empresariales como FactuWeb PRO, las rutas de administración se agrupan en un bloque maestro. Esto garantiza que **todas** las rutas hijas queden automáticamente protegidas con una sola declaración:

```php
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\ProductoController;
use Illuminate\Auth\Middleware\Authenticate;

Route::middleware([Authenticate::class, 'role:ADMIN'])
    ->prefix('admin')      // Antepone '/admin' a todas las URLs hijas
    ->name('admin.')        // Antepone 'admin.' a todos los nombres de rutas
    ->group(function () {
        
        // URL real: /admin/dashboard | Nombre: admin.dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        // URL real: /admin/usuario | Nombre: admin.usuario.index
        Route::get('/usuario', [UsuarioController::class, 'index'])
            ->name('usuario.index');

        // URL real: /admin/producto | Nombre: admin.producto.index
        Route::get('/producto', [ProductoController::class, 'index'])
            ->name('producto.index');
    });
```

#### Ventajas de esta técnica:
1. **Sin duplicación:** Si cambias el middleware de rol o el prefijo, se actualiza en todas las rutas del grupo.
2. **Imposible olvidar la protección:** Cualquier ruta declarada dentro del callback `{ ... }` nace protegida.
3. **Nombres organizados:** Al usar `->name('admin.')`, la ruta `->name('usuario.index')` se convierte automáticamente en `route('admin.usuario.index')`.

---

### Técnica B: Protección en Ruta Individual
Si tienes una ruta específica fuera de un grupo y quieres protegerla para uno o más roles:
```php
Route::get('/auditoria', [AuditoriaController::class, 'index'])
    ->middleware(['auth', 'role:ADMIN,SUPERVISOR'])
    ->name('auditoria.index');
```

---

### Técnica C: Protección en el Constructor del Controlador
En Laravel 11 / 12 / 13, puedes proteger un controlador completo o métodos individuales mediante la interfaz `HasMiddleware`:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UsuarioController extends Controller implements HasMiddleware
{
    /**
     * Define los middlewares que aplican a este controlador.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('role:ADMIN', only: ['create', 'store', 'destroy']),
        ];
    }
}
```

---

## 4. Cómo Crear y Conectar la Vista Blade

### 4.1. Crear el Archivo de Vista con Artisan
Ejecuta:
```bash
php artisan make:view Admin.producto.index
```
Esto crea automáticamente la carpeta y el archivo en:
`resources/views/Admin/producto/index.blade.php`

---

### 4.2. Estructura Estándar de una Vista Vinculada al Layout
Abre el archivo recién creado y conéctalo con tu layout maestro (`layouts.sidebaradmin`):

```blade
@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Catálogo de Productos - FactuWeb PRO')

@section('content')
<div class="container-fluid p-0">

    <!-- Encabezado de la página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #12102f;">
                <i class="fa-solid fa-box-open me-2 text-primary"></i>Productos
            </h4>
            <small class="text-muted">Administra el inventario y precios de venta</small>
        </div>
        <div>
            <a href="{{ route('admin.producto.create') }}" class="btn btn-warning text-white fw-bold">
                <i class="fa-solid fa-plus me-1"></i>Nuevo Producto
            </a>
        </div>
    </div>

    <!-- Contenedor / Tabla de Registros -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Precio Venta</th>
                        <th>Stock</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($productos as $producto)
                        <tr>
                            <td>{{ $producto->codigo_barras }}</td>
                            <td class="fw-semibold">{{ $producto->nombre_producto }}</td>
                            <td>${{ number_format($producto->precio_venta, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge {{ $producto->stock_actual > 5 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $producto->stock_actual }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.producto.edit', $producto) }}" class="btn btn-sm btn-light border">
                                    <i class="fa-solid fa-pen text-primary"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                No hay productos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if ($productos->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $productos->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
```

---

## 5. Protección Visual en Blade (Menús, Botones y Enlaces Condicionales)

Aunque el middleware protege la URL contra accesos no autorizados, es indispensable **ocultar los elementos visuales** para usuarios que no tengan el rol permitido.

### 5.1. Condicional de Rol para Menús del Sidebar
En `resources/views/layouts/sidebaradmin.blade.php`:

```blade
{{-- Opción visible ÚNICAMENTE para Administradores --}}
@if (strtoupper(auth()->user()->role?->code ?? '') === 'ADMIN')
    <li class="nav-item">
        <a href="{{ route('admin.usuario.index') }}" 
           class="nav-link {{ request()->routeIs('admin.usuario.*') ? 'active' : '' }}">
            <i class="fa-solid fa-user-shield"></i> Empleados
        </a>
    </li>
@endif
```

### 5.2. Condicional para Botones Críticos de Acción
En tablas o vistas de detalle, oculta botones sensibles (como eliminar o editar):

```blade
@if (strtoupper(auth()->user()->role?->code ?? '') === 'ADMIN')
    <form action="{{ route('admin.producto.destroy', $producto) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Seguro que deseas eliminar este registro?')">
            <i class="fa-solid fa-trash"></i> Eliminar
        </button>
    </form>
@endif
```

### 5.3. Mantener el Enlace Activo en Menús (Clase `active`)
Para que el menú del sidebar se ilumine automáticamente en naranja cuando navegas en la ruta actual o en cualquiera de sus sub-rutas (crear, editar, ver):
```blade
class="nav-link {{ request()->routeIs('admin.producto.*') ? 'active' : '' }}"
```
*El comodín `.*` hace que coincida tanto con `admin.producto.index` como con `admin.producto.create` o `admin.producto.edit`.*

---

## 6. Catálogo de Errores Críticos y Cómo Evitarlos

A continuación se listan los errores más comunes cometidos al enrutar y crear vistas en Laravel, junto con su causa exacta y cómo prevenirlos:

### Error 1: `Class "App\Http\Controllers\Admin\User" not found`
- **Causa:** En el controlador escribes `User::paginate()` o `Role::all()`, pero olvidaste colocar la sentencia `use` al inicio del archivo. Al estar dentro del namespace `App\Http\Controllers\Admin`, PHP asume que el modelo se encuentra en esa misma carpeta.
- **Solución:**
  ```php
  use App\Models\User; // <-- Añadir siempre al inicio
  use App\Models\Role;
  ```

---

### Error 2: `Invalid route action: [UsuarioController]`
- **Causa:** En `routes/web.php` escribiste:
  ```php
  // ERROR: Separado por comas fuera de un array
  Route::get('/admin/usuario', UsuarioController::class, 'index');
  ```
- **Solución:** Los argumentos deben encapsularse dentro de corchetes `[...]`:
  ```php
  // CORRECTO:
  Route::get('/usuario', [UsuarioController::class, 'index'])->name('usuario.index');
  ```

---

### Error 3: URLs duplicadas como `/admin/admin/usuario`
- **Causa:** El grupo de rutas ya tiene definido `->prefix('admin')`. Si dentro del grupo vuelves a escribir `/admin/usuario`, Laravel suma ambos prefijos.
  ```php
  Route::prefix('admin')->group(function () {
      Route::get('/admin/usuario', ...); // Genera /admin/admin/usuario (INCORRECTO)
  });
  ```
- **Solución:** La ruta hija solo debe declarar el segmento restante:
  ```php
  Route::prefix('admin')->group(function () {
      Route::get('/usuario', ...); // Genera /admin/usuario (CORRECTO)
  });
  ```

---

### Error 4: Discrepancia de Mayúsculas / Minúsculas en Roles (`403 Forbidden`)
- **Causa:** En la base de datos el rol se guardó como `'ADMIN'`, pero en el código comparas con `'admin'` (o viceversa), causando que la comparación estricta `===` falle.
- **Solución:** Normaliza siempre ambas cadenas con `strtoupper()` o `strtolower()`:
  ```php
  // En middleware o blade:
  if (strtoupper($user->role?->code ?? '') !== 'ADMIN') {
      abort(403);
  }
  ```

---

### Error 5: `TypeError: Return value must be of type Illuminate\View\View, none returned`
- **Causa:** El método declara `: View` pero olvidaste escribir la palabra reservada `return`:
  ```php
  public function index(): View
  {
      $usuarios = User::paginate(10);
      view('Admin.usuario.index', compact('usuarios')); // ERROR: Falta return
  }
  ```
- **Solución:**
  ```php
  return view('Admin.usuario.index', compact('usuarios'));
  ```

---

### Error 6: `Route [admin.dashboard] not found` en el IDE
- **Causa:** Ocurre cuando hay un error de sintaxis en cualquier parte de `routes/web.php` (por ejemplo, una coma mal puesta o un controlador no importado). Laravel no puede terminar de compilar el árbol de rutas y marca como inexistentes incluso las rutas que están bien escritas.
- **Solución:** Ejecuta siempre en la consola:
  ```bash
  php artisan route:list
  ```
  La terminal te indicará la línea exacta del archivo `web.php` donde se produjo la excepción.

---

## 7. Paso a Paso Completo con un Ejemplo Real (Módulo "Clientes")

Imaginemos que deseamos crear el módulo de **Clientes** para FactuWeb PRO, accesible **solo para Administradores**:

### Paso 1: Crear Controlador y Vista
```bash
php artisan make:controller Admin/ClienteController
php artisan make:view Admin.cliente.index
```

### Paso 2: Programar el Controlador (`app/Http/Controllers/Admin/ClienteController.php`)
```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function index(): View
    {
        $clientes = Cliente::orderBy('razon_social')->paginate(10);

        return view('Admin.cliente.index', compact('clientes'));
    }
}
```

### Paso 3: Registrar la Ruta Protegida en `routes/web.php`
```php
use App\Http\Controllers\Admin\ClienteController;

Route::middleware([Authenticate::class, 'role:ADMIN'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        
        // Nueva ruta de clientes
        Route::get('/clientes', [ClienteController::class, 'index'])
            ->name('clientes.index');
    });
```

### Paso 4: Programar la Vista (`resources/views/Admin/cliente/index.blade.php`)
```blade
@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Clientes - FactuWeb PRO')

@section('content')
<div class="container-fluid p-0">
    <h4 class="fw-bold mb-3" style="color: #12102f;">Directorio de Clientes</h4>
    
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
        <p class="text-muted">Listado de clientes registrados en el sistema.</p>
        <!-- Tu tabla aquí -->
    </div>
</div>
@endsection
```

### Paso 5: Enlazar en el Sidebar (`resources/views/layouts/sidebaradmin.blade.php`)
```blade
@if (strtoupper(auth()->user()->role?->code ?? '') === 'ADMIN')
    <li class="nav-item">
        <a href="{{ route('admin.clientes.index') }}" 
           class="nav-link {{ request()->routeIs('admin.clientes.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users"></i> Clientes
        </a>
    </li>
@endif
```

### Paso 6: Verificar en Terminal
```bash
php artisan route:list --name=clientes
```
Salida confirmada:
```text
GET|HEAD admin/clientes .. admin.clientes.index › Admin\ClienteController@index
```

---

## 8. Checklist Rápido de Validación

Antes de dar por finalizada la creación de una vista protegida, revisa esta lista de control:

- [ ] ¿El modelo está importado con `use App\Models\NombreModelo;` en el controlador?
- [ ] ¿La clase `View` está importada con `use Illuminate\View\View;` si usas tipado `: View`?
- [ ] ¿El método tiene la palabra `return view('carpeta.vista', compact(...));`?
- [ ] ¿La ruta en `routes/web.php` usa la sintaxis de array `[MiControlador::class, 'metodo']`?
- [ ] ¿El controlador está importado al inicio de `routes/web.php`?
- [ ] ¿La ruta está dentro de un grupo con el middleware `'role:ADMIN'` o el rol correspondiente?
- [ ] ¿La URL hija no duplica el prefijo del grupo?
- [ ] ¿La vista Blade contiene `@extends('layouts.sidebaradmin')` y `@section('content')`?
- [ ] ¿El enlace en el sidebar usa `route('nombre.de.ruta')` y valida el rol con `@if`?
- [ ] ¿Ejecutaste `php artisan route:list` y la consola terminó con código de salida `0` sin excepciones?
