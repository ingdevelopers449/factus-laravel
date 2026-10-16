<?php

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

        // RUTA METODO POST DEL MODAL DE CREAR EMPLEADO
        Route::post('/admin/usuario', [
            \App\Http\Controllers\Admin\UsuarioController::class,
            'store',
        ])->name('usuario.store');
    });

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
