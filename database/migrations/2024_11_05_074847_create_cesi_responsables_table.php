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
        Schema::create('cesi_responsables', function (Blueprint $table) {
            $table->id();
            $table->string('responsable_usuario')->unique();
            $table->string('responsable_contraseña')->nullable();
            $table->string('responsable_nombre')->nullable();
            $table->string('responsable_telefono')->nullable();
            $table->string('responsable_foto')->nullable();
            $table->boolean('responsable_activacion')->default(0);
            $table->foreignId('cesi_tutore_id')->constrained()->cascadeOnDelete();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cesi_responsables');
    }
};
