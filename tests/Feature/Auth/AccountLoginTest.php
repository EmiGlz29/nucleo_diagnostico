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

    Schema::create('Doctor', function (Blueprint $table) {
        $table->integer('codigo')->primary();
        $table->string('nombre');
    });

    Schema::create('Paciente', function (Blueprint $table) {
        $table->integer('codigo')->primary();
        $table->string('nombre');
        $table->string('direccion');
        $table->string('telefono');
        $table->date('fecha_nac');
        $table->string('sexo');
        $table->integer('edad');
        $table->decimal('estatura', 5, 2);
    });

    DB::table('Empleado')->insert(['codigo' => 1, 'nombre' => 'Igual', 'contraseña' => 'empleado']);
    DB::table('Administrador')->insert(['codigo' => 1, 'nombre' => 'Igual', 'contraseña' => '1234']);
});

test('administrator login restores the right account and protects creation routes', function () {
    $this->post(route('login.store'), [
        'nombre' => 'Igual',
        'password' => '1234',
    ])->assertRedirect(route('menu-principal', absolute: false));

    expect(Hash::check('1234', DB::table('Administrador')->value('contraseña')))->toBeTrue();

    Auth::forgetGuards();
    $this->get(route('menu-principal'))->assertOk()
        ->assertSee('Agregar Empleados')
        ->assertSee('Agregar Doctores')
        ->assertSee('Agregar Paciente');
    expect(Auth::user())->toBeInstanceOf(Administrador::class);

    $this->get(route('empleados.create'))->assertOk();
    $this->get(route('doctores.create'))->assertOk();
    $this->get(route('pacientes.create'))->assertOk();
    $this->post(route('empleados.store'), [])->assertSessionHasErrors();
    $this->post(route('doctores.store'), [])->assertSessionHasErrors();

    $this->post(route('logout'))->assertRedirect('/');
    $this->post(route('login.store'), [
        'nombre' => 'Igual',
        'password' => '1234',
    ])->assertRedirect(route('menu-principal', absolute: false));
});

test('employee login does not inherit administrator permissions with the same code and name', function () {
    $this->post(route('login.store'), [
        'nombre' => 'Igual',
        'password' => 'empleado',
    ])->assertRedirect(route('menu-principal', absolute: false));

    Auth::forgetGuards();
    $this->get(route('menu-principal'))->assertOk()
        ->assertDontSee('Agregar Empleados')
        ->assertDontSee('Agregar Doctores')
        ->assertSee('Agregar Paciente');
    expect(Auth::user())->toBeInstanceOf(EMPLEADO::class);

    $this->get(route('empleados.create'))->assertForbidden();
    $this->post(route('empleados.store'), [])->assertForbidden();
    $this->get(route('doctores.create'))->assertForbidden();
    $this->post(route('doctores.store'), [])->assertForbidden();
    $this->get(route('profile.edit'))->assertForbidden();
    $this->get(route('empleados.index'))->assertOk();
    $this->get(route('doctores.index'))->assertOk();
    $this->get(route('pacientes.index'))->assertOk();
    $this->get(route('pacientes.create'))->assertOk();
    $this->post(route('pacientes.store'), [
        'codigo' => 42,
        'nombre' => 'Paciente de prueba',
        'direccion' => 'Direccion de prueba',
        'telefono' => '1234567890',
        'fecha_nac' => '2000-01-01',
        'sexo' => 'F',
        'edad' => 26,
        'estatura' => 165,
    ])->assertRedirect(route('menu-principal'));

    $this->assertDatabaseHas('Paciente', ['codigo' => 42, 'nombre' => 'Paciente de prueba']);
});

test('invalid credentials cannot authenticate either account', function () {
    $this->post(route('login.store'), [
        'nombre' => 'Igual',
        'password' => 'incorrecta',
    ])->assertSessionHasErrors('nombre');

    $this->assertGuest();
});

test('matching credentials in both tables give priority to the administrator', function () {
    DB::table('Empleado')->where('codigo', 1)->update(['contraseña' => '1234']);

    $this->post(route('login.store'), [
        'nombre' => 'Igual',
        'password' => '1234',
        'tipo_cuenta' => 'empleado',
    ])->assertRedirect(route('menu-principal', absolute: false));

    Auth::forgetGuards();
    $this->get(route('menu-principal'))->assertOk()->assertSee('Agregar Empleados');
    expect(Auth::user())->toBeInstanceOf(Administrador::class);
});
