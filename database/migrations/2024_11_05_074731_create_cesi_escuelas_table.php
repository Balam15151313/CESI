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
        Schema::create('cesi_escuelas', function (Blueprint $table) {
            $table->id();
            $table->string('escuela_nombre')->nullable();
            $table->string('escuela_escolaridad')->nullable();
            $table->string('escuela_latitud')->nullable();
            $table->string('escuela_longitud')->nullable();
            $table->string('escuela_logo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cesi_escuelas');
    }
};
