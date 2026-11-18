<?php

use App\Models\Administrador;
use App\Models\EMPLEADO;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('Empleado', function (Blueprint $table) {
        $table->integer('codigo')->primary();
        $table->string('nombre');
        $table->string('contraseña');
        $table->rememberToken();
    });

    Schema::create('Administrador', function (Blueprint $table) {
        $table->integer('codigo')->primary();
        $table->string('nombre', 50);
        $table->string('contraseña', 255);
    });

    DB::table('Empleado')->insert(['codigo' => 1, 'nombre' => 'Igual', 'contraseña' => 'empleado']);
    DB::table('Administrador')->insert(['codigo' => 1, 'nombre' => 'Igual', 'contraseña' => '1234']);
});

test('administrator login restores the right account and protects creation routes', function () {
    $this->post(route('login.store'), [
        'nombre' => 'Igual',
        'password' => '1234',
        'tipo_cuenta' => 'administrador',
    ])->assertRedirect(route('menu-principal', absolute: false));

    expect(Hash::check('1234', DB::table('Administrador')->value('contraseña')))->toBeTrue();

    Auth::forgetGuards();
    $this->get(route('menu-principal'))->assertOk()
        ->assertSee('Agregar Empleados')
        ->assertSee('Agregar Doctores');
    expect(Auth::user())->toBeInstanceOf(Administrador::class);

    $this->get(route('empleados.create'))->assertOk();
    $this->get(route('doctores.create'))->assertOk();
    $this->post(route('empleados.store'), [])->assertSessionHasErrors();
    $this->post(route('doctores.store'), [])->assertSessionHasErrors();

    $this->post(route('logout'))->assertRedirect('/');
    $this->post(route('login.store'), [
        'nombre' => 'Igual',
        'password' => '1234',
        'tipo_cuenta' => 'administrador',
    ])->assertRedirect(route('menu-principal', absolute: false));
});

test('employee login does not inherit administrator permissions with the same code and name', function () {
    $this->post(route('login.store'), [
        'nombre' => 'Igual',
        'password' => 'empleado',
        'tipo_cuenta' => 'empleado',
    ])->assertRedirect(route('menu-principal', absolute: false));

    Auth::forgetGuards();
    $this->get(route('menu-principal'))->assertOk()
        ->assertDontSee('Agregar Empleados')
        ->assertDontSee('Agregar Doctores');
    expect(Auth::user())->toBeInstanceOf(EMPLEADO::class);

    $this->get(route('empleados.create'))->assertForbidden();
    $this->post(route('empleados.store'), [])->assertForbidden();
    $this->get(route('doctores.create'))->assertForbidden();
    $this->post(route('doctores.store'), [])->assertForbidden();
});

test('credentials are checked only against the selected account type', function () {
    $this->post(route('login.store'), [
        'nombre' => 'Igual',
        'password' => '1234',
        'tipo_cuenta' => 'empleado',
    ])->assertSessionHasErrors('nombre');

    $this->assertGuest();
});
