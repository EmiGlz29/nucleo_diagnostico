<?php

use App\Models\Doctor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    DB::table('Administrador')->insert([
        'codigo' => 3,
        'nombre' => 'JOSUE FRANCISCO RAMIREZ LUGO',
        'contraseña' => 'clave-compartida',
    ]);

    DB::table('Empleado')->insert([
        'codigo' => 3,
        'nombre' => 'JOSUE FRANCISCO RAMIREZ LUGO',
        'direccion' => 'Calle de prueba',
        'telefono' => '3312345678',
        'fecha_nac' => '1984-03-02',
        'sexo' => 'MASCULINO',
        'salario' => 10000,
        'turno' => 'MATUTINO',
        'contraseña' => 'clave-compartida',
    ]);

    Doctor::query()->create([
        'codigo' => 3,
        'nombre' => 'JOSUE FRANCISCO RAMIREZ LUGO',
        'direccion' => 'TOLEDO 2588',
        'telefono' => '3334903175',
        'fecha_nac' => '1984-03-02',
        'sexo' => 'MASCULINO',
        'especialidad' => 'GASTROENTEROLOGIA',
        'contraseña' => 'clave-compartida',
    ]);
});

test('doctor selection disambiguates login by code and full name', function () {
    $credentials = [
        'nombre' => '3',
        'password' => 'clave-compartida',
        'tipo_cuenta' => 'doctor',
    ];

    $this->post(route('login.store'), $credentials)
        ->assertRedirect(route('menu-principal', absolute: false));

    Auth::forgetGuards();
    $this->get(route('menu-principal'))->assertOk();
    expect(Auth::user())->toBeInstanceOf(Doctor::class);

    $this->post(route('logout'));
    $this->post(route('login.store'), [
        ...$credentials,
        'nombre' => 'JOSUE FRANCISCO RAMIREZ LUGO',
    ])->assertRedirect(route('menu-principal', absolute: false));

    Auth::forgetGuards();
    $this->get(route('menu-principal'))->assertOk();
    expect(Auth::user())->toBeInstanceOf(Doctor::class);
});
