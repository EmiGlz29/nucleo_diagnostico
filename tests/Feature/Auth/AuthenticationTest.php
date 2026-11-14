<?php

use App\Models\EMPLEADO;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

beforeEach(function () {
    Schema::create('Empleado', function (Blueprint $table) {
        $table->increments('codigo');
        $table->string('nombre');
        $table->string('contraseña', 255);
    });

    $this->employee = EMPLEADO::query()->create([
        'nombre' => 'Empleado de prueba',
        'contraseña' => 'clave-legada',
    ]);
});

test('login screen can be rendered', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
});

test('employees can authenticate with their name and legacy password', function () {
    $response = $this->post(route('login.store'), [
        'nombre' => $this->employee->nombre,
        'password' => 'clave-legada',
    ]);

    $this->assertAuthenticatedAs($this->employee);
    $response->assertRedirect(route('dashboard', absolute: false));
    expect(password_get_info($this->employee->fresh()->getAttribute('contraseña'))['algo'])
        ->not->toBeNull();
    expect(Hash::check('clave-legada', $this->employee->fresh()->getAttribute('contraseña')))
        ->toBeTrue();
});

test('employees can not authenticate with an invalid password', function () {
    $this->post(route('login.store'), [
        'nombre' => $this->employee->nombre,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('employees can update their profile name', function () {
    $this->actingAs($this->employee)
        ->patch(route('profile.update'), ['name' => 'Nombre actualizado'])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));

    expect($this->employee->fresh()->nombre)->toBe('Nombre actualizado');
});

test('employees can update their password', function () {
    $this->employee->setAttribute('contraseña', Hash::make('clave-legada'));
    $this->employee->save();

    $this->actingAs($this->employee)
        ->put(route('user-password.update'), [
            'current_password' => 'clave-legada',
            'password' => 'Nueva-clave-123',
            'password_confirmation' => 'Nueva-clave-123',
        ])
        ->assertSessionHasNoErrors();

    expect(Hash::check(
        'Nueva-clave-123',
        $this->employee->fresh()->getAttribute('contraseña'),
    ))->toBeTrue();
});

test('employees cannot delete their employee record from profile settings', function () {
    $this->employee->setAttribute('contraseña', Hash::make('clave-legada'));
    $this->employee->save();

    $this->actingAs($this->employee)
        ->delete(route('profile.destroy'), ['password' => 'clave-legada'])
        ->assertForbidden();

    expect($this->employee->fresh())->not->toBeNull();
});

test('employees can logout', function () {
    $response = $this->actingAs($this->employee)->post(route('logout'));

    $response->assertRedirect(route('home'));

    $this->assertGuest();
});

test('employee login attempts are rate limited', function () {
    RateLimiter::increment(md5('login'.implode('|', [
        Str::transliterate(Str::lower($this->employee->nombre)),
        '127.0.0.1',
    ])), amount: 5);

    $response = $this->post(route('login.store'), [
        'nombre' => $this->employee->nombre,
        'password' => 'wrong-password',
    ]);

    $response->assertTooManyRequests();
});
