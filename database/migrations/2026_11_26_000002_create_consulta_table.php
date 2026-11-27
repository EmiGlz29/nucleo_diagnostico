<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'Medicamento' => ['codigo'],
            'Cita' => ['codigocita', 'codigopaciente'],
            'Doctor' => ['codigo'],
        ] as $table => $columns) {
            if (! Schema::hasTable($table)) {
                throw new RuntimeException("No se puede crear Consulta: falta la tabla {$table}.");
            }

            foreach ($columns as $column) {
                if (! Schema::hasColumn($table, $column)) {
                    throw new RuntimeException("No se puede crear Consulta: falta {$table}.{$column}.");
                }
            }
        }

        if (Schema::hasTable('Consulta')) {
            foreach ([
                'codigoconsulta',
                'codigocita',
                'codigopaciente',
                'codigodoctor',
                'diagnostico',
                'codigo_medicamento',
                'dias_tratamiento',
                'horario_suministro',
                'fecha_consulta',
            ] as $column) {
                if (! Schema::hasColumn('Consulta', $column)) {
                    throw new RuntimeException("La tabla Consulta existe pero no tiene la columna {$column}.");
                }
            }

            return;
        }

        Schema::create('Consulta', function (Blueprint $table): void {
            $table->increments('codigoconsulta');
            $table->integer('codigocita');
            $table->integer('codigopaciente');
            $table->integer('codigodoctor');
            $table->text('diagnostico');
            $table->integer('codigo_medicamento');
            $table->unsignedSmallInteger('dias_tratamiento');
            $table->string('horario_suministro', 255);
            $table->timestamp('fecha_consulta')->useCurrent();

            $table->foreign(['codigocita', 'codigopaciente'])
                ->references(['codigocita', 'codigopaciente'])
                ->on('Cita')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreign('codigodoctor')
                ->references('codigo')->on('Doctor')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreign('codigo_medicamento')
                ->references('codigo')->on('Medicamento')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Consulta');
    }
};
