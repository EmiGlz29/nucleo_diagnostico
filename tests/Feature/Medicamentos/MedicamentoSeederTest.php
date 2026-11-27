<?php

use App\Models\Administrador;
use App\Models\EMPLEADO;
use App\Models\Medicamento;
use Database\Seeders\MedicamentoSeeder;
use Illuminate\Support\Facades\DB;

test('medication seeder safely keeps exactly the four required catalog rows', function () {
    $this->seed(MedicamentoSeeder::class);
    $this->seed(MedicamentoSeeder::class);

    $this->assertDatabaseCount('Medicamento', 4);
    $this->assertDatabaseHas('Medicamento', [
        'codigo' => 3,
        'nombre_med' => 'DICLOFENACO VIT B1, B6 Y B12',
        'via_admin' => 'ORAL',
        'presentacion' => 'TABLETA',
        'fecha_cad' => '2028-03-31',
    ]);
});

test('administrator can create and update trimmed medication fields', function () {
    DB::table('Administrador')->insert([
        'codigo' => 1,
        'nombre' => 'ADMINISTRADOR DE PRUEBA',
        'contraseña' => 'clave',
    ]);

    $this->actingAs(Administrador::query()->findOrFail(1))
        ->get(route('medicamentos.create'))
        ->assertOk()
        ->assertSee('Registrar medicamento')
        ->assertSee('<nav class="sidebar" aria-label="Navegación principal">', false)
        ->assertSee('Agregar medicamento</a>', false)
        ->assertSee('Cerrar Sesión');

    $this->post(route('medicamentos.store'), [
        'codigo' => ' 10 ',
        'nombre_med' => ' PARACETAMOL PRUEBA ',
        'via_admin' => ' ORAL ',
        'presentacion' => ' TABLETA ',
        'fecha_cad' => '2028-01-31',
    ])->assertRedirect(route('medicamentos.index'));

    $medicamento = Medicamento::query()->findOrFail(10);
    expect($medicamento->nombre_med)->toBe('PARACETAMOL PRUEBA');
    expect($medicamento->via_admin)->toBe('ORAL');

    $this->get(route('medicamentos.index'))
        ->assertOk()
        ->assertSee('PARACETAMOL PRUEBA')
        ->assertSee('<nav class="sidebar" aria-label="Navegación principal">', false)
        ->assertSee('href="'.route('citas.create').'"', false)
        ->assertSee('Volver al Menú Principal');

    $this->put(route('medicamentos.update', $medicamento), [
        'codigo' => '10',
        'nombre_med' => ' AMOXICILINA PRUEBA ',
        'via_admin' => ' ORAL ',
        'presentacion' => ' CAPSULA ',
        'fecha_cad' => '2028-04-30',
    ])->assertRedirect(route('medicamentos.index'));

    expect($medicamento->fresh()->nombre_med)->toBe('AMOXICILINA PRUEBA');
});

test('employees see the medication sidebar without administration links', function () {
    DB::table('Empleado')->insert([
        'codigo' => 1,
        'nombre' => 'EMPLEADO PRUEBA',
        'direccion' => 'CALLE UNO',
        'telefono' => '3311111111',
        'fecha_nac' => '1990-01-01',
        'sexo' => 'FEMENINO',
        'salario' => 1000,
        'turno' => 'MATUTINO',
        'contraseña' => 'clave',
    ]);

    $this->actingAs(EMPLEADO::query()->findOrFail(1))
        ->get(route('medicamentos.index'))
        ->assertOk()
        ->assertSee('<nav class="sidebar" aria-label="Navegación principal">', false)
        ->assertSee('Hola,')
        ->assertDontSee('href="'.route('medicamentos.create').'"', false);
});
