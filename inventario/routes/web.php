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
use App\Http\Controllers\DashboardController;


// Rutas de login, logout y recuperación de contraseña.
// El registro público está desactivado: los usuarios los crea un administrador.
Auth::routes(['register' => false]);

// Todo lo que hay dentro de este grupo exige tener la sesión iniciada.
// Si alguien no ha entrado, se le redirige al login.
Route::middleware('auth')->group(function () {
    // Panel de inicio con el resumen del inventario
    Route::get('/', DashboardController::class)->name('dashboard');
    // /home es la ruta a la que Laravel manda tras el login; la reenviamos al panel
    Route::get('/home', fn () => redirect()->route('dashboard'))->name('home');

    // CRUD completo (listar, crear, ver, editar, borrar) de cada entidad
    Route::resource('productos', ProductoController::class);
    Route::resource('sedes', SedeController::class)->except('show');
    Route::resource('categorias', CategoriaController::class)->except('show');
    // Laravel no sabe pluralizar bien "trabajadores" en español,
    // así que se indica a mano el nombre del parámetro de la ruta
    Route::resource('trabajadores', TrabajadorController::class)
        ->parameters(['trabajadores' => 'trabajador']);

    // Asignar un producto a un trabajador (formulario + guardado)
    Route::get('productos/{producto}/asignar', [AsignacionController::class, 'create'])
        ->name('productos.asignar');
    Route::post('productos/{producto}/asignar', [AsignacionController::class, 'store'])
        ->name('productos.asignar.store');

    // Registrar la devolución de una asignación (PATCH porque modifica una asignación ya existente)
    Route::get('asignaciones/{asignacion}/devolver', [AsignacionController::class, 'devolverForm'])
        ->name('asignaciones.devolver');
    Route::patch('asignaciones/{asignacion}/devolver', [AsignacionController::class, 'devolver'])
        ->name('asignaciones.devolver.store');

    // Perfil propio: cada usuario edita su nombre y contraseña
    Route::get('perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('perfil', [PerfilController::class, 'update'])->name('perfil.update');

    // Gestión de usuarios: solo para administradores (Gate "admin", definido en AppServiceProvider).
    // No hay "destroy" a propósito: los usuarios se desactivan, no se borran.
    Route::middleware('can:admin')->group(function () {
        Route::resource('usuarios', UsuarioController::class)->except(['show', 'destroy']);
    });
});