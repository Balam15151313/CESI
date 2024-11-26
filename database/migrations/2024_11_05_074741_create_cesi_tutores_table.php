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
        Schema::create('cesi_tutores', function (Blueprint $table) {
            $table->id();
            $table->string('tutor_usuario')->unique();
            $table->string('tutor_contraseña')->nullable();
            $table->string('tutor_nombre')->nullable();
            $table->string('tutor_telefono')->nullable();
            $table->string('tutor_foto')->nullable();
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
        Schema::dropIfExists('cesi_tutores');
    }
};
