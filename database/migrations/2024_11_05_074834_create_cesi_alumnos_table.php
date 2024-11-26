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
        Schema::create('cesi_alumnos', function (Blueprint $table) {
            $table->id();
            $table->string('alumno_nombre')->nullable();
            $table->date('alumno_nacimiento')->nullable();
            $table->string('alumno_foto')->nullable();
            $table->foreignId('cesi_salon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cesi_tutore_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cesi_alumnos');
    }
};
