<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\SedeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\TrabajadorController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\UsuarioController;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes(['register' => false]);

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::resource('productos', ProductoController::class);
    Route::resource('sedes', SedeController::class)->except('show');
    Route::resource('categorias', CategoriaController::class)->except('show');
    Route::resource('trabajadores', TrabajadorController::class)
        ->parameters(['trabajadores' => 'trabajador']);

    Route::get('productos/{producto}/asignar', [AsignacionController::class, 'create'])
        ->name('productos.asignar');
    Route::post('productos/{producto}/asignar', [AsignacionController::class, 'store'])
        ->name('productos.asignar.store');

    Route::get('asignaciones/{asignacion}/devolver', [AsignacionController::class, 'devolverForm'])
        ->name('asignaciones.devolver');
    Route::patch('asignaciones/{asignacion}/devolver', [AsignacionController::class, 'devolver'])
        ->name('asignaciones.devolver.store');
    Route::get('perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('perfil', [PerfilController::class, 'update'])->name('perfil.update');

    Route::middleware('can:admin')->group(function () {
        Route::resource('usuarios', UsuarioController::class)->except(['show', 'destroy']);
    });
});