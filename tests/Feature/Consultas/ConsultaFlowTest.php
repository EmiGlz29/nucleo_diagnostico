<?php

use App\Models\Consulta;
use App\Models\Doctor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    Doctor::query()->create([
        'codigo' => 3,
        'nombre' => 'JOSUE FRANCISCO RAMIREZ LUGO',
        'direccion' => 'TOLEDO 2588',
        'telefono' => '3334903175',
        'fecha_nac' => '1984-03-02',
        'sexo' => 'MASCULINO',
        'especialidad' => 'GASTROENTEROLOGIA',
        'contraseña' => '2031984',
    ]);
    Doctor::query()->create([
        'codigo' => 4,
        'nombre' => 'OTRA DOCTORA',
        'direccion' => 'CALLE TRES',
        'telefono' => '3333333333',
        'fecha_nac' => '1985-01-01',
        'sexo' => 'FEMENINO',
        'especialidad' => 'MEDICINA GENERAL',
        'contraseña' => 'otra-clave',
    ]);

    DB::table('Paciente')->insert([
        [
            'codigo' => 11,
            'nombre' => 'PACIENTE UNO',
            'direccion' => 'CALLE UNO',
            'telefono' => '3311111111',
            'fecha_nac' => '1990-01-01',
            'sexo' => 'FEMENINO',
            'edad' => 36,
            'estatura' => 1.65,
        ],
        [
            'codigo' => 12,
            'nombre' => 'PACIENTE DOS',
            'direccion' => 'CALLE DOS',
            'telefono' => '3322222222',
            'fecha_nac' => '1980-02-02',
            'sexo' => 'MASCULINO',
            'edad' => 46,
            'estatura' => 1.80,
        ],
    ]);

    DB::table('Cita')->insert([
        [
            'codigocita' => 21,
            'codigopaciente' => 11,
            'codigodoctor' => 3,
            'fechacita' => now()->addDay()->toDateString(),
            'hora' => '10:00:00',
        ],
        [
            'codigocita' => 22,
            'codigopaciente' => 12,
            'codigodoctor' => 4,
            'fechacita' => now()->addDays(2)->toDateString(),
            'hora' => '11:00:00',
        ],
    ]);

    DB::table('Medicamento')->insert([
        'codigo' => 1,
        'nombre_med' => 'PARACETAMOL',
        'via_admin' => 'ORAL',
        'presentacion' => 'TABLETA',
        'fecha_cad' => '2027-01-31',
    ]);
});

function logInAsDoctorForConsultaFlow($test): void
{
    $test->post(route('login.store'), [
        'nombre' => '3',
        'password' => '2031984',
        'tipo_cuenta' => 'doctor',
    ])->assertRedirect(route('menu-principal', absolute: false));
    Auth::forgetGuards();
    $test->get(route('menu-principal'))->assertOk();
}

test('doctor sees their appointments and all patients', function () {
    logInAsDoctorForConsultaFlow($this);

    $this->get(route('citas.index'))
        ->assertOk()
        ->assertSee('PACIENTE UNO')
        ->assertSee('Atender Consulta')
        ->assertDontSee('PACIENTE DOS');

    $this->get(route('pacientes.index'))
        ->assertOk()
        ->assertSee('Próxima cita')
        ->assertSee('Iniciar consulta')
        ->assertSee('PACIENTE UNO')
        ->assertSee('PACIENTE DOS');

    $this->get(route('consultas.create', [22, 12]))->assertForbidden();
});

test('doctor sees all patients without an upcoming appointment', function () {
    DB::table('Cita')->where('codigodoctor', 3)->delete();
    logInAsDoctorForConsultaFlow($this);

    $this->get(route('pacientes.index'))
        ->assertOk()
        ->assertSee('No tienes próximas citas asignadas.')
        ->assertSee('PACIENTE UNO')
        ->assertSee('PACIENTE DOS');
});

test('administrator sees appointments for every doctor', function () {
    DB::table('Administrador')->insert([
        'codigo' => 1,
        'nombre' => 'ADMINISTRADOR DE PRUEBA',
        'contraseña' => 'admin-clave',
    ]);

    $this->post(route('login.store'), [
        'nombre' => '1',
        'password' => 'admin-clave',
        'tipo_cuenta' => 'administrador',
    ])->assertRedirect(route('menu-principal', absolute: false));
    Auth::forgetGuards();
    $this->get(route('menu-principal'))->assertOk();

    $this->get(route('citas.index'))
        ->assertOk()
        ->assertSee('PACIENTE UNO')
        ->assertSee('PACIENTE DOS');
});

test('doctor saves a sanitized consultation and downloads receta.pdf', function () {
    logInAsDoctorForConsultaFlow($this);

    $this->get(route('consultas.create', [21, 11]))
        ->assertOk()
        ->assertSee('PARACETAMOL');

    $response = $this->post(route('consultas.store'), [
        'codigocita' => ' 21 ',
        'codigopaciente' => ' 11 ',
        'codigo_med' => ' 1 ',
        'diagnostico' => '  Diagnóstico de prueba  ',
        'dias_tratamiento' => ' 5 ',
        'horario_suministro' => ' Cada 8 horas ',
    ]);

    $consulta = Consulta::query()->firstOrFail();
    expect($consulta->diagnostico)->toBe('Diagnóstico de prueba');
    expect($consulta->horario_suministro)->toBe('Cada 8 horas');
    $response->assertRedirect(route('consultas.show', $consulta));

    $this->get(route('consultas.show', $consulta))
        ->assertOk()
        ->assertSee('GENERAR RECETA')
        ->assertSee('PACIENTE UNO')
        ->assertSee('JOSUE FRANCISCO RAMIREZ LUGO');

    $pdf = $this->get(route('consultas.receta', $consulta));
    $pdf->assertOk()->assertHeader('content-type', 'application/pdf');
    expect($pdf->headers->get('content-disposition'))->toContain('receta.pdf');
    expect(substr($pdf->getContent(), 0, 5))->toBe('%PDF-');
});
