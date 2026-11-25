<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('Administrador')) {
            Schema::create('Administrador', function (Blueprint $table) {
                $table->integer('codigo')->primary();
                $table->string('nombre');
                $table->string('contraseña');
            });
        }

        if (! Schema::hasTable('Empleado')) {
            Schema::create('Empleado', function (Blueprint $table) {
                $table->integer('codigo')->primary();
                $table->string('nombre');
                $table->string('direccion');
                $table->string('telefono', 15);
                $table->date('fecha_nac');
                $table->string('sexo', 20);
                $table->decimal('salario', 10, 2);
                $table->string('turno', 20);
                $table->string('contraseña');
            });
        }

        if (! Schema::hasTable('Doctor')) {
            Schema::create('Doctor', function (Blueprint $table) {
                $table->integer('codigo')->primary();
                $table->string('nombre');
                $table->string('direccion');
                $table->string('telefono', 15);
                $table->date('fecha_nac');
                $table->string('sexo', 20);
                $table->string('especialidad', 100);
                $table->string('contraseña');
            });
        }

        if (! Schema::hasTable('Paciente')) {
            Schema::create('Paciente', function (Blueprint $table) {
                $table->integer('codigo')->primary();
                $table->string('nombre');
                $table->string('direccion');
                $table->string('telefono', 15);
                $table->date('fecha_nac');
                $table->string('sexo', 20);
                $table->integer('edad');
                $table->decimal('estatura', 4, 2);
            });
        }

        foreach ([
            'Administrador' => ['codigo', 'nombre', 'contraseña'],
            'Empleado' => ['codigo', 'nombre', 'direccion', 'telefono', 'fecha_nac', 'sexo', 'salario', 'turno', 'contraseña'],
            'Doctor' => ['codigo', 'nombre', 'direccion', 'telefono', 'fecha_nac', 'sexo', 'especialidad', 'contraseña'],
            'Paciente' => ['codigo', 'nombre', 'direccion', 'telefono', 'fecha_nac', 'sexo', 'edad', 'estatura'],
        ] as $table => $columns) {
            foreach ($columns as $column) {
                if (! Schema::hasColumn($table, $column)) {
                    throw new RuntimeException("La tabla {$table} existe pero no tiene la columna requerida {$column}.");
                }
            }
        }
    }

    public function down(): void
    {
        // These legacy tables may predate Laravel migrations and contain production records.
    }
};
