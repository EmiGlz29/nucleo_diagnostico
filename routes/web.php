<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('dashboard', '/menu-principal')->name('dashboard');
    Route::view('menu-principal', 'MenuPrincipal')->name('menu-principal');
});

require __DIR__.'/settings.php';
