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
        Schema::create('cesi_recogidas', function (Blueprint $table) {
            $table->id();
            $table->timestamp('recogida_fecha')->nullable();
            $table->longText('recogida_observaciones');
            $table->string('recogida_estatus')->nullable();
            $table->string('recogida_qr')->nullable();
            $table->foreignId('cesi_responsable_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cesi_recogidas');
    }
};
