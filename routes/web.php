<?php

use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Empleado\DashboardController as EmpleadoDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return match (auth()->user()?->role?->code) {
        'admin' => redirect()->route('admin.dashboard'),
        'empleado' => redirect()->route('empleado.dashboard'),
        default => redirect()->route('login'),
    };
})->middleware(Authenticate::class)->name('dashboard');

Route::middleware([Authenticate::class, EnsureUserHasRole::class.':admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');
        Route::get('/usuario', [UsuarioController::class, 'index'])
            ->name('usuario.index');
        Route::get('/clientes', [ClienteController::class, 'index'])
            ->name('clientes.index');

        // RUTA METODO POST DEL MODAL DE CREAR EMPLEADO
        Route::post('/admin/usuario', [
            UsuarioController::class,
            'store',
        ])->name('usuario.store');

        // RUTA METODO POST DEL MODAL DE CREAR CLIENTE
        Route::post('/admin/clientes', [
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
