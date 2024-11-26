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
        Schema::create('cesi_rastreos', function (Blueprint $table) {
            $table->id();
            $table->double('rastreo_longitud')->nullable();
            $table->double('rastreo_latitud')->nullable();
            $table->foreignId('cesi_recogida_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cesi_rastreos');
    }
};
