<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['Doctor', 'Paciente', 'Empleado'] as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'codigo')) {
                throw new RuntimeException("No se puede crear cita: falta la tabla {$table} o su columna codigo.");
            }

            if (! in_array(Schema::getColumnType($table, 'codigo'), [
                'integer', 'bigint', 'smallint', 'int2', 'int4', 'int8',
            ], true)) {
                throw new RuntimeException("La columna {$table}.codigo debe ser de tipo entero para conservar la integridad referencial.");
            }
        }

        if (Schema::hasTable('Cita')) {
            foreach (['codigocita', 'codigopaciente', 'codigodoctor', 'fechacita', 'hora', 'codigoempleado'] as $column) {
                if (! Schema::hasColumn('Cita', $column)) {
                    throw new RuntimeException("La tabla Cita existe pero no tiene la columna requerida {$column}.");
                }
            }

            if (! in_array(Schema::getColumnType('Cita', 'codigocita'), ['integer', 'int4'], true)) {
                throw new RuntimeException('La columna Cita.codigocita debe ser de tipo entero.');
            }

            return;
        }

        if (Schema::hasTable('cita')) {
            throw new RuntimeException('La tabla cita ya existe; verifique manualmente su clave primaria y sus llaves foráneas antes de continuar.');
        }

        Schema::create('Cita', function (Blueprint $table) {
            $table->integer('codigocita')->primary();
            $this->addCodeColumn($table, Schema::getColumnType('Paciente', 'codigo'), 'codigopaciente');
            $this->addCodeColumn($table, Schema::getColumnType('Doctor', 'codigo'), 'codigodoctor')->nullable();
            $table->date('fechacita');
            $table->time('hora');
            $this->addCodeColumn($table, Schema::getColumnType('Empleado', 'codigo'), 'codigoempleado');

            $table->foreign('codigopaciente')
                ->references('codigo')->on('Paciente')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreign('codigodoctor')
                ->references('codigo')->on('Doctor')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreign('codigoempleado')
                ->references('codigo')->on('Empleado')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        // Cita may predate this migration and contain existing records.
    }

    private function addCodeColumn(Blueprint $table, string $type, string $column): \Illuminate\Database\Schema\ColumnDefinition
    {
        return match ($type) {
            'smallint', 'int2' => $table->smallInteger($column),
            'integer', 'int4' => $table->integer($column),
            'bigint', 'int8' => $table->bigInteger($column),
        };
    }
};
