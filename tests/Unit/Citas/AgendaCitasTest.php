<?php

use App\Models\Administrador;
use App\Models\EMPLEADO;
use Carbon\CarbonImmutable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::dropIfExists('Cita');
    Schema::dropIfExists('Paciente');
    Schema::dropIfExists('Doctor');
    Schema::dropIfExists('Empleado');
    Schema::dropIfExists('Administrador');

    Schema::create('Empleado', function (Blueprint $table) {
        $table->integer('codigo')->primary();
        $table->string('nombre');
        $table->string('contraseña');
        $table->rememberToken();
    });
    Schema::create('Administrador', function (Blueprint $table) {
        $table->integer('codigo')->primary();
        $table->string('nombre');
        $table->string('contraseña');
    });
    Schema::create('Doctor', function (Blueprint $table) {
        $table->integer('codigo')->primary();
        $table->string('nombre');
        $table->string('especialidad')->default('Medicina general');
    });
    Schema::create('Paciente', function (Blueprint $table) {
        $table->integer('codigo')->primary();
        $table->string('nombre');
    });

    DB::table('Empleado')->insert(['codigo' => 1, 'nombre' => 'BEATRIZ', 'contraseña' => 'password']);
    DB::table('Administrador')->insert(['codigo' => 1, 'nombre' => 'ADMIN', 'contraseña' => 'password']);
    DB::table('Doctor')->insert([
        ['codigo' => 10, 'nombre' => 'Doctora Uno'],
        ['codigo' => 11, 'nombre' => 'Doctor Dos'],
    ]);
    DB::table('Paciente')->insert([
        ['codigo' => 20, 'nombre' => 'Paciente Uno'],
        ['codigo' => 21, 'nombre' => 'Paciente Dos'],
    ]);

    $migration = require database_path('migrations/2026_11_24_000000_create_cita_table.php');
    $migration->up();
    $this->actingAs(EMPLEADO::query()->findOrFail(1));
});

function nextWeekday(): string
{
    return CarbonImmutable::now()->next(CarbonImmutable::MONDAY)->toDateString();
}

function citaPayload(array $overrides = []): array
{
    return array_merge([
        'codigocita' => 1,
        'codigopaciente' => 20,
        'codigodoctor' => 10,
        'fechacita' => nextWeekday(),
        'hora' => '09:00',
    ], $overrides);
}

test('employees register appointments with an integer code and their employee key', function () {
    $this->get(route('citas.create'))->assertOk()->assertSee('Agenda médica');

    $this->post(route('citas.store'), citaPayload(['codigoempleado' => 999]))
        ->assertRedirect(route('citas.create'))->assertSessionHasNoErrors();
    $this->assertDatabaseHas('Cita', ['codigocita' => 1, 'codigodoctor' => 10, 'codigoempleado' => 1]);
    $this->get(route('citas.create'))->assertSee('value="2"', false);
});

test('administrators cannot register appointments even when their code matches an employee', function () {
    $this->actingAs(Administrador::query()->findOrFail(1));
    $this->get(route('citas.create'))->assertSee('Solo los empleados pueden registrar citas.');
    $this->post(route('citas.store'), citaPayload())->assertForbidden();
    $this->assertDatabaseCount('Cita', 0);
});

test('appointment codes must be unique valid integers', function () {
    $this->post(route('citas.store'), citaPayload(['codigocita' => 'CIT-001']))->assertSessionHasErrors('codigocita');
    $this->post(route('citas.store'), citaPayload(['codigocita' => 2147483648]))->assertSessionHasErrors('codigocita');
    $this->post(route('citas.store'), citaPayload())->assertSessionHasNoErrors();
    $this->post(route('citas.store'), citaPayload(['hora' => '10:00']))->assertSessionHasErrors('codigocita');
});

test('meal break and conflicting appointments are rejected', function () {
    $this->post(route('citas.store'), citaPayload(['hora' => '14:00']))->assertSessionHasErrors('hora');
    $this->post(route('citas.store'), citaPayload())->assertSessionHasNoErrors();
    $this->post(route('citas.store'), citaPayload(['codigocita' => 2, 'codigopaciente' => 21]))->assertSessionHasErrors('hora');
    $this->post(route('citas.store'), citaPayload(['codigocita' => 3, 'codigodoctor' => 11]))->assertSessionHasErrors('codigopaciente');
});

test('appointments can be edited without changing their employee and appear in the agenda', function () {
    $this->post(route('citas.store'), citaPayload())->assertSessionHasNoErrors();
    $this->get(route('citas.edit', 1))->assertOk();
    $this->put(route('citas.update', 1), citaPayload(['hora' => '10:00', 'codigoempleado' => 999]))
        ->assertSessionHasNoErrors();
    $this->assertDatabaseHas('Cita', ['codigocita' => 1, 'hora' => '10:00', 'codigoempleado' => 1]);
    $this->put(route('citas.update', 1), citaPayload(['fechacita' => '2026-11-20']))
        ->assertSessionHasErrors('fechacita');
    $this->get(route('citas.agenda', ['codigodoctor' => 10, 'fecha' => nextWeekday()]))
        ->assertOk()->assertJsonPath('citas.0.codigocita', 1)->assertJsonMissingPath('sql');
});

test('appointments are allowed from November 23 2026 even when the date and time have passed', function () {
    $this->travelTo(CarbonImmutable::parse('2026-11-25 18:00:00'));

    $this->post(route('citas.store'), citaPayload(['fechacita' => '2026-11-20']))
        ->assertSessionHasErrors('fechacita');
    $this->post(route('citas.store'), citaPayload(['fechacita' => '2026-11-23']))
        ->assertSessionHasNoErrors();
    $this->put(route('citas.update', 1), citaPayload(['fechacita' => '2026-11-24']))
        ->assertSessionHasNoErrors();
    $this->assertDatabaseHas('Cita', ['codigocita' => 1, 'fechacita' => '2026-11-24']);
    $this->get(route('citas.create'))->assertSee('const minAppointmentDate = "2026-11-23"', false);
});

test('migration leaves an existing Cita table and its records intact', function () {
    $this->post(route('citas.store'), citaPayload())->assertSessionHasNoErrors();
    $migration = require database_path('migrations/2026_11_24_000000_create_cita_table.php');
    $migration->up();
    $migration->down();
    $this->assertDatabaseHas('Cita', ['codigocita' => 1, 'codigoempleado' => 1]);
});
