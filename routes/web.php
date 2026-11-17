<?php

use App\Http\Controllers\EMPLEADOController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('dashboard', '/menu-principal')->name('dashboard');
    Route::view('menu-principal', 'MenuPrincipal')->name('menu-principal');

    // Rutas para empleados
    Route::get('/empleados/crear', [EMPLEADOController::class, 'create'])->name('empleados.create');
    Route::post('/empleados', [EMPLEADOController::class, 'store'])->name('empleados.store');

    Route::get('/empleados/mostrar', [EMPLEADOController::class, 'index'])->name('empleados.index');
});

require __DIR__.'/settings.php';
