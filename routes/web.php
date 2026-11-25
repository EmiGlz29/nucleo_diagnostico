<?php

use App\Http\Controllers\CitaController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\EMPLEADOController;
use App\Http\Controllers\PacienteController;
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
    Route::get('/doctores/{doctor}/agenda', [DoctorController::class, 'agenda'])->name('doctores.agenda');
    Route::get('/doctores/mostrar', [DoctorController::class, 'index'])->name('doctores.index');

    Route::get('/citas/crear', [CitaController::class, 'create'])->name('citas.create');
    Route::get('/citas/agenda', [CitaController::class, 'agenda'])->name('citas.agenda');
    Route::post('/citas', [CitaController::class, 'store'])->name('citas.store');
    Route::get('/citas/{codigocita}/editar', [CitaController::class, 'edit'])->name('citas.edit');
    Route::put('/citas/{codigocita}', [CitaController::class, 'update'])->name('citas.update');
    Route::get('/citas/mostrar', [CitaController::class, 'index'])->name('citas.index');

    // Rutas para pacientes
    Route::get('/pacientes/crear', [PacienteController::class, 'create'])->name('pacientes.create');
    Route::post('/pacientes', [PacienteController::class, 'store'])->name('pacientes.store');

    Route::get('/pacientes/mostrar', [PacienteController::class, 'index'])->name('pacientes.index');

});

require __DIR__.'/settings.php';
