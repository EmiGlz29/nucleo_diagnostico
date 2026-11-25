<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class ProyectoFinalSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Administrador', 'Empleado', 'Doctor', 'Paciente', 'cita'] as $table) {
            if (! Schema::hasTable($table)) {
                throw new RuntimeException("No se puede cargar el proyecto: falta la tabla {$table}. Ejecute php artisan migrate primero.");
            }
        }

        DB::transaction(function (): void {
            DB::table('Administrador')->updateOrInsert(
                ['codigo' => 1],
                ['nombre' => 'Admin Principal', 'contraseña' => '123456'],
            );

            DB::table('Empleado')->upsert([
                [
                    'codigo' => 1,
                    'nombre' => 'BEATRIZ MEDINA CHICUATE',
                    'direccion' => 'COIMBRA 869',
                    'telefono' => '3313669040',
                    'fecha_nac' => '1972-02-07',
                    'sexo' => 'FEMENINO',
                    'salario' => 14500.70,
                    'turno' => 'MATUTINO',
                    'contraseña' => '7021972',
                ],
                [
                    'codigo' => 2,
                    'nombre' => 'JANET VALENCIA CARBAJAL',
                    'direccion' => 'HIDALGO 986',
                    'telefono' => '3314129537',
                    'fecha_nac' => '1987-10-31',
                    'sexo' => 'FEMENINO',
                    'salario' => 14999.90,
                    'turno' => 'VESPERTINO',
                    'contraseña' => '31101987',
                ],
            ], ['codigo'], [
                'nombre', 'direccion', 'telefono', 'fecha_nac', 'sexo', 'salario', 'turno', 'contraseña',
            ]);

            DB::table('Doctor')->upsert([
                [
                    'codigo' => 1,
                    'nombre' => 'PAOLA ELIZABETH DIAZ ACEVES',
                    'direccion' => 'COLINA CENTRAL 200',
                    'telefono' => '3329853910',
                    'fecha_nac' => '1985-01-29',
                    'sexo' => 'FEMENINO',
                    'especialidad' => 'GERIATRIA',
                    'contraseña' => '29011985',
                ],
                [
                    'codigo' => 2,
                    'nombre' => 'CLAUDIA ELIZABETH GOMEZ LOPEZ',
                    'direccion' => 'MARIANO OTERO 300',
                    'telefono' => '3301053911',
                    'fecha_nac' => '1985-05-01',
                    'sexo' => 'FEMENINO',
                    'especialidad' => 'REUMATOLOGIA',
                    'contraseña' => '1051985',
                ],
                [
                    'codigo' => 3,
                    'nombre' => 'JOSUE FRANCISCO RAMIREZ LUGO',
                    'direccion' => 'TOLEDO 2588',
                    'telefono' => '3334903175',
                    'fecha_nac' => '1984-03-02',
                    'sexo' => 'MASCULINO',
                    'especialidad' => 'GASTROENTEROLOGIA',
                    'contraseña' => '2031984',
                ],
                [
                    'codigo' => 4,
                    'nombre' => 'CHRISTOPHER MENESES ARREOLA',
                    'direccion' => 'CLUB ORO 400',
                    'telefono' => '3314108419',
                    'fecha_nac' => '1984-10-14',
                    'sexo' => 'MASCULINO',
                    'especialidad' => 'CARDIOLOGIA',
                    'contraseña' => '14101984',
                ],
            ], ['codigo'], [
                'nombre', 'direccion', 'telefono', 'fecha_nac', 'sexo', 'especialidad', 'contraseña',
            ]);

            DB::table('Paciente')->upsert([
                [
                    'codigo' => 1,
                    'nombre' => 'JOB GODOY GAMBOA',
                    'direccion' => 'PALMA REAL 7691',
                    'telefono' => '3313480524',
                    'fecha_nac' => '1967-08-14',
                    'sexo' => 'MASCULINO',
                    'edad' => 59,
                    'estatura' => 1.77,
                ],
                [
                    'codigo' => 2,
                    'nombre' => 'FRANCISCO ELIAS REYNOSO PRESA',
                    'direccion' => 'JUAN DEL CARMEN 891',
                    'telefono' => '3311737312',
                    'fecha_nac' => '1980-07-19',
                    'sexo' => 'MASCULINO',
                    'edad' => 46,
                    'estatura' => 1.83,
                ],
                [
                    'codigo' => 3,
                    'nombre' => 'ALAN SANTIAGO CORTES GODINEZ',
                    'direccion' => 'VOLCAN SAN FCO 8891',
                    'telefono' => '3310615592',
                    'fecha_nac' => '1988-11-23',
                    'sexo' => 'MASCULINO',
                    'edad' => 38,
                    'estatura' => 1.83,
                ],
                [
                    'codigo' => 4,
                    'nombre' => 'DOLORES MEDINA CHICUATE',
                    'direccion' => 'COIMBRA 869',
                    'telefono' => '3313669039',
                    'fecha_nac' => '1968-10-15',
                    'sexo' => 'FEMENINO',
                    'edad' => 58,
                    'estatura' => 1.73,
                ],
                [
                    'codigo' => 5,
                    'nombre' => 'GUADALUPE LUGO ROJAS',
                    'direccion' => 'JUAREZ 3691',
                    'telefono' => '3314517285',
                    'fecha_nac' => '1936-10-27',
                    'sexo' => 'FEMENINO',
                    'edad' => 90,
                    'estatura' => 1.55,
                ],
                [
                    'codigo' => 6,
                    'nombre' => 'LUZ ROBLES JIMENEZ',
                    'direccion' => 'LITOGRAFIA 5591',
                    'telefono' => '3334530438',
                    'fecha_nac' => '1956-08-11',
                    'sexo' => 'FEMENINO',
                    'edad' => 69,
                    'estatura' => 1.65,
                ],
            ], ['codigo'], [
                'nombre', 'direccion', 'telefono', 'fecha_nac', 'sexo', 'edad', 'estatura',
            ]);

            DB::table('cita')->truncate();
        });

        $this->command?->info('Registros oficiales cargados. Citas vaciadas para iniciar las pruebas.');
        $this->command?->line('Admin Principal / código 1 / 123456');
        $this->command?->line('BEATRIZ MEDINA CHICUATE / código 1 / 7021972');
        $this->command?->line('JANET VALENCIA CARBAJAL / código 2 / 31101987');
    }
}
