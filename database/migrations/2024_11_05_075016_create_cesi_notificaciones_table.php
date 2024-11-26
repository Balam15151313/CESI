<?php

use App\Models\cesi_alumno;
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
        Schema::create('cesi_notificaciones', function (Blueprint $table) {
            $table->id();
            $table->longText('notificaciones_mensaje')->nullable();
            $table->string('notificaciones_prioridad')->nullable();
            $table->string('notificaciones_tipo')->nullable();
            $table->foreignId('cesi_alumno_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cesi_notificaciones');
    }
};
