<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DoctorJosueSeeder extends Seeder
{
    public function run(): void
    {
        $nombre = 'JOSUE FRANCISCO RAMIREZ LUGO';
        $doctor = Doctor::query()->find(3);

        if ($doctor !== null) {
            if ($doctor->nombre !== $nombre) {
                throw new RuntimeException('El código 3 ya pertenece a otro doctor; no se modificó ningún registro.');
            }

            $this->command?->info('El doctor Josué ya existe; no se realizaron cambios.');

            return;
        }

        if (Doctor::query()->where('nombre', $nombre)->exists()) {
            throw new RuntimeException('El doctor Josué ya existe con otro código; no se modificó ningún registro.');
        }

        Doctor::query()->create([
            'codigo' => 3,
            'nombre' => $nombre,
            'direccion' => 'TOLEDO 2588',
            'telefono' => '3334903175',
            'fecha_nac' => '1984-03-02',
            'sexo' => 'MASCULINO',
            'especialidad' => 'GASTROENTEROLOGIA',
            'contraseña' => Hash::make('2031984'),
        ]);

        $this->command?->info('Se creó el registro faltante del doctor Josué.');
    }
}
