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
        Schema::create('cesi_administradors', function (Blueprint $table) {
            $table->id();
            $table->string('administrador_usuario')->unique();
            $table->string('administrador_contraseña')->nullable();
            $table->string('administrador_nombre')->nullable();
            $table->string('administrador_telefono')->nullable();
            $table->string('administrador_foto')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cesi_administradors');
    }
};
