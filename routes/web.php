<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('dashboard', '/menu-principal')->name('dashboard');
    Route::view('menu-principal', 'MenuPrincipal')->name('menu-principal');
    
    // Rutas para empleados
    Route::get('/empleados/crear', [\App\Http\Controllers\EMPLEADOController::class, 'create'])->name('empleados.create');
    Route::post('/empleados', [\App\Http\Controllers\EMPLEADOController::class, 'store'])->name('empleados.store');
});

require __DIR__.'/settings.php';
