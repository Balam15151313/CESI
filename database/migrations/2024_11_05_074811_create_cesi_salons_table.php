<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cesi_salons', function (Blueprint $table) {
            $table->id();
            $table->string('salon_grado')->nullable();
            $table->string('salon_grupo')->nullable();
            $table->foreignId('cesi_escuela_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cesi_maestro_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cesi_salons');
    }
};
