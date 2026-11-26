<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('Paciente', function (Blueprint $table) {
            $table->decimal('estatura', 5, 2)->change();
        });
    }

    public function down(): void
    {
        Schema::table('Paciente', function (Blueprint $table) {
            $table->decimal('estatura', 4, 2)->change();
        });
    }
};
