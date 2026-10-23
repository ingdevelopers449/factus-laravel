<?php

use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Admin\EmpresaController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Empleado\DashboardController as EmpleadoDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\EnsureUserHasRole;
use App\Models\Categoria;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function (Request $request) {
    $roleCode = strtolower((string) $request->user()?->role?->code);

    return match ($roleCode) {
        'admin' => redirect()->route('admin.dashboard'),
        'empleado' => redirect()->route('empleado.dashboard'),
        default => redirect()->route('login'),
    };
})->middleware(Authenticate::class)->name('dashboard');

Route::middleware([Authenticate::class, EnsureUserHasRole::class.':admin'])
    ->prefix('admin')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('admin.dashboard');

        // RUTA VISTA GESTION DE USUARIO
        Route::get('/usuario', [UsuarioController::class, 'index'])
            ->name('usuario.index');
        // RUTA VISTA DE GESTION DE CLIENTES
        Route::get('/cliente', [ClienteController::class, 'index'])
            ->name('clientes.index');
        // RUTA VISTA DE CATEGORIAS
        Route::get('/categorias', [CategoriaController::class, 'index'])
            ->name('categorias.index');
        // RUTA VISTA DE EMPRESA
        Route::get('/empresas', [EmpresaController::class, 'index'])
            ->name('empresas.index');

        // RUTAS DE MODULO PRODUCTOS
        Route::get('/producto', [ProductoController::class, 'index'])
            ->name('producto.index');
        Route::post('/producto', [ProductoController::class, 'store'])
            ->name('producto.store');
        Route::put('/producto/{producto}', [ProductoController::class, 'update'])
            ->name('producto.update');
        Route::delete('/producto/{producto}', [ProductoController::class, 'destroy'])
            ->name('producto.destroy');

        // RUTA METODO POST DEL MODAL DE CREAR EMPLEADO
        Route::post('/usuario', [
            UsuarioController::class,
            'store',
        ])->name('usuario.store');

        // RUTA METODO POST DEL MODAL DE CREAR CLIENTE
        Route::post('/clientes', [
            ClienteController::class,
            'store',
        ])->name('clientes.store');

        // BTN DE ACCIÓN ACTUALIZAR CLIENTE
        Route::put('clientes/{cliente}', [ClienteController::class,
            'update',
        ])->name('clientes.update');

        // BTN DE ACCIÓN ELIMIANR CLIENTE
        Route::delete('clientes/{cliente}', [ClienteController::class, 'destroy'])
            ->name('clientes.destroy');

        // BTN DE ACCIÓN ACTUALIZAR EMPLEADO/USUARIO
        Route::put('usuario/{usuario}', [UsuarioController::class,
            'update',
        ])->name('usuario.update');

        // BTN DE ACCIÓN ELIMIANAR EMPLEADO/USUARIO
        Route::delete('usuario/{usuario}', [UsuarioController::class, 'destroy'])
            ->name('usuario.destroy');

        // RUTA STORE CATEGORIA
        Route::post('/categorias', [CategoriaController::class, 'store'])
            ->name('categorias.store');

        // RUTA UPDATE CATEGORIA
        Route::put('/categorias/{id}', [CategoriaController::class, 'update'])
            ->name('categorias.update');

        // RUTA STORE CATEGORIA
        Route::delete('/categorias/{id}', [CategoriaController::class, 'destroy'])
            ->name('categorias.destroy');
    }); // CIERRE DEL MIDDLWARE DE ADMIN

Route::middleware([Authenticate::class, EnsureUserHasRole::class.':empleado'])
    ->prefix('empleado')
    ->name('empleado.')
    ->group(function () {
        Route::get('/dashboard', [EmpleadoDashboardController::class, 'index'])
            ->name('dashboard');
    });

Route::middleware(Authenticate::class)->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
