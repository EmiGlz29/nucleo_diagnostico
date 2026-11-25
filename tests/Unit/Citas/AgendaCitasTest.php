<?php

use App\Models\EMPLEADO;
use Carbon\CarbonImmutable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::dropIfExists('cita');
    Schema::dropIfExists('Paciente');
    Schema::dropIfExists('Doctor');
    Schema::dropIfExists('Empleado');

    Schema::create('Empleado', function (Blueprint $table) {
        $table->integer('codigo')->primary();
        $table->string('nombre');
        $table->string('contraseña');
        $table->rememberToken();
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
        'codigocita' => 'CIT-001',
        'codigopaciente' => 20,
        'codigodoctor' => 10,
        'fechacita' => nextWeekday(),
        'hora' => '09:00',
    ], $overrides);
}

test('appointment migration adds the custom primary and foreign keys', function () {
    expect(Schema::hasTable('cita'))->toBeTrue()
        ->and(Schema::hasColumn('cita', 'codigocita'))->toBeTrue();

    $this->get(route('citas.create'))->assertOk()->assertSee('Agenda médica');

    $this->post(route('citas.store'), citaPayload())->assertRedirect(route('citas.create'));
    $this->assertDatabaseHas('cita', ['codigocita' => 'CIT-001', 'codigodoctor' => 10]);
});

test('meal break is rejected and its attempted SQL is shown', function () {
    $this->post(route('citas.store'), citaPayload(['hora' => '14:00']))
        ->assertSessionHasErrors([
            'hora' => 'No se puede registrar la cita: horario de comida del doctor (2:00 PM a 4:00 PM)',
        ]);

    $this->assertDatabaseCount('cita', 0);
});

test('doctor and patient schedule conflicts are rejected', function () {
    DB::table('cita')->insert([
        'codigocita' => 'CIT-001',
        'codigopaciente' => 20,
        'codigodoctor' => 10,
        'fechacita' => nextWeekday(),
        'hora' => '09:00',
    ]);

    $this->post(route('citas.store'), citaPayload(['codigocita' => 'CIT-002', 'codigopaciente' => 21]))
        ->assertSessionHasErrors('hora');

    $this->post(route('citas.store'), citaPayload([
        'codigocita' => 'CIT-003',
        'codigodoctor' => 11,
    ]))->assertSessionHasErrors('codigopaciente');
});

test('past appointments cannot be reprogrammed and the schedule endpoint reports prepared SQL', function () {
    DB::table('cita')->insert([
        'codigocita' => 'CIT-001',
        'codigopaciente' => 20,
        'codigodoctor' => 10,
        'fechacita' => nextWeekday(),
        'hora' => '09:00',
    ]);

    $this->put(route('citas.update', 'CIT-001'), citaPayload([
        'fechacita' => today()->subDay()->toDateString(),
        'hora' => '10:00',
    ]))->assertSessionHasErrors('fechacita');

    $this->get(route('citas.agenda', [
        'codigodoctor' => 10,
        'fecha' => nextWeekday(),
    ]))->assertOk()
        ->assertJsonPath('citas.0.codigocita', 'CIT-001')
        ->assertJsonMissingPath('sql');
});
