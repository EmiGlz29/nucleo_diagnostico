<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('Medicamento')) {
            foreach (['codigo', 'nombre_med', 'via_admin', 'presentacion', 'fecha_cad'] as $column) {
                if (! Schema::hasColumn('Medicamento', $column)) {
                    throw new RuntimeException("La tabla Medicamento existe pero no tiene la columna {$column}.");
                }
            }

            return;
        }

        Schema::create('Medicamento', function (Blueprint $table): void {
            $table->integer('codigo')->primary();
            $table->string('nombre_med', 255);
            $table->string('via_admin', 255);
            $table->string('presentacion', 255);
            $table->date('fecha_cad');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Medicamento');
    }
};
