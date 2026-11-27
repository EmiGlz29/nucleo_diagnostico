<?php

namespace Database\Seeders;

use App\Models\Medicamento;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicamentoSeeder extends Seeder
{
    public function run(): void
    {
        $medicamentos = [
            [
                'codigo' => 1,
                'nombre_med' => 'PARACETAMOL',
                'via_admin' => 'ORAL',
                'presentacion' => 'TABLETA',
                'fecha_cad' => '2027-01-31',
            ],
            [
                'codigo' => 2,
                'nombre_med' => 'CLORFENAMINA COMPUESTA',
                'via_admin' => 'ORAL',
                'presentacion' => 'TABLETA',
                'fecha_cad' => '2027-02-28',
            ],
            [
                'codigo' => 3,
                'nombre_med' => 'DICLOFENACO VIT B1, B6 Y B12',
                'via_admin' => 'ORAL',
                'presentacion' => 'TABLETA',
                'fecha_cad' => '2028-03-31',
            ],
            [
                'codigo' => 4,
                'nombre_med' => 'AMOXICILINA',
                'via_admin' => 'ORAL',
                'presentacion' => 'CAPSULA',
                'fecha_cad' => '2028-04-30',
            ],
        ];

        DB::transaction(function () use ($medicamentos): void {
            foreach ($medicamentos as $medicamento) {
                Medicamento::query()->updateOrCreate(
                    ['codigo' => $medicamento['codigo']],
                    $medicamento,
                );
            }
        });
    }
}
