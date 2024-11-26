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
        Schema::create('cesi_maestros', function (Blueprint $table) {
            $table->id();
            $table->string('maestro_usuario')->unique();
            $table->string('maestro_contraseña')->nullable();
            $table->string('maestro_nombre')->nullable();
            $table->string('maestro_telefono')->nullable();
            $table->string('maestro_foto')->nullable();
            $table->foreignId('cesi_escuela_id')->constrained()->cascadeOnDelete();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cesi_maestros');
    }
};
