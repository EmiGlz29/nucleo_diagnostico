<?php

use App\Http\Controllers\DoctorController;
use App\Http\Controllers\EMPLEADOController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('dashboard', '/menu-principal')->name('dashboard');
    Route::view('menu-principal', 'MenuPrincipal')->name('menu-principal');

    // Rutas para empleados
    Route::get('/empleados/crear', [EMPLEADOController::class, 'create'])->middleware('can:gestionar-altas')->name('empleados.create');
    Route::post('/empleados', [EMPLEADOController::class, 'store'])->middleware('can:gestionar-altas')->name('empleados.store');

    Route::get('/empleados/mostrar', [EMPLEADOController::class, 'index'])->name('empleados.index');

    // Rutas para Doctores
    Route::get('/doctores/crear', [DoctorController::class, 'create'])->middleware('can:gestionar-altas')->name('doctores.create');
    Route::post('/doctores', [DoctorController::class, 'store'])->middleware('can:gestionar-altas')->name('doctores.store');

    Route::get('/doctores/mostrar', [DoctorController::class, 'index'])->name('doctores.index');
});

require __DIR__.'/settings.php';
