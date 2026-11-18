<?php

use App\Models\EMPLEADO;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('Empleado', function (Blueprint $table) {
        $table->id('codigo');
        $table->string('nombre');
        $table->string('contraseña');
        $table->rememberToken();
    });
});

test('the main menu submits a protected logout request', function () {
    $empleado = EMPLEADO::create([
        'nombre' => 'Primer empleado',
        'contraseña' => 'password1',
    ]);

    $this->actingAs($empleado)
        ->get(route('menu-principal'))
        ->assertOk()
        ->assertSee('<form method="POST" action="'.route('logout').'">', false)
        ->assertSee('name="_token"', false)
        ->assertSee('<button type="submit">Cerrar sesión</button>', false);
});

test('an employee can log out and sign in as another employee', function () {
    $first = EMPLEADO::create([
        'nombre' => 'Primer empleado',
        'contraseña' => 'password1',
    ]);
    $second = EMPLEADO::create([
        'nombre' => 'Segundo empleado',
        'contraseña' => 'password2',
    ]);

    $this->actingAs($first)
        ->withSession(['previous_employee_data' => 'private'])
        ->post(route('logout'))
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull(session('previous_employee_data'));
    $this->get(route('menu-principal'))->assertRedirect(route('login'));
    $this->get('/')->assertRedirect('/login');

    $this->post(route('login.store'), [
        'nombre' => $second->nombre,
        'password' => 'password2',
    ])->assertRedirect(route('menu-principal', absolute: false));

    $this->assertAuthenticatedAs($second);
    $this->get(route('menu-principal'))
        ->assertOk()
        ->assertSee($second->nombre);
});