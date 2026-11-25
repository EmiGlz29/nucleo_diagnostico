<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['Doctor', 'Paciente'] as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'codigo')) {
                throw new RuntimeException("No se puede crear cita: falta la tabla {$table} o su columna codigo.");
            }

            if (! in_array(Schema::getColumnType($table, 'codigo'), [
                'integer', 'bigint', 'smallint', 'int2', 'int4', 'int8',
            ], true)) {
                throw new RuntimeException("La columna {$table}.codigo debe ser de tipo entero para conservar la integridad referencial.");
            }
        }

        $doctorCodeType = Schema::getColumnType('Doctor', 'codigo');
        $patientCodeType = Schema::getColumnType('Paciente', 'codigo');

        if (Schema::hasTable('cita')) {
            throw new RuntimeException('La tabla cita ya existe; verifique manualmente su clave primaria y sus llaves foráneas antes de continuar.');
        }

        Schema::create('cita', function (Blueprint $table) use ($doctorCodeType, $patientCodeType) {
            $table->string('codigocita', 20)->primary();
            $this->addCodeColumn($table, $patientCodeType, 'codigopaciente');
            $this->addCodeColumn($table, $doctorCodeType, 'codigodoctor');
            $table->date('fechacita');
            $table->time('hora');

            $table->foreign('codigopaciente')
                ->references('codigo')->on('Paciente')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreign('codigodoctor')
                ->references('codigo')->on('Doctor')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->unique(['codigodoctor', 'fechacita', 'hora'], 'cita_doctor_fecha_hora_unique');
            $table->unique(['codigopaciente', 'fechacita', 'hora'], 'cita_paciente_fecha_hora_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cita');
    }

    private function addCodeColumn(Blueprint $table, string $type, string $column): void
    {
        match ($type) {
            'smallint', 'int2' => $table->smallInteger($column),
            'integer', 'int4' => $table->integer($column),
            'bigint', 'int8' => $table->bigInteger($column),
        };
    }
};
