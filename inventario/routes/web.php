<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\SedeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\TrabajadorController;
use App\Http\Controllers\ProductoController;

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
});