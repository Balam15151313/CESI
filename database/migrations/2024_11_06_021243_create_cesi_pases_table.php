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
        Schema::create('cesi_pases', function (Blueprint $table) {
            $table->id();
            $table->string('pase_status');
            $table->foreignId('cesi_alumno_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cesi_asistencia_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cesi_pases');
    }
};
