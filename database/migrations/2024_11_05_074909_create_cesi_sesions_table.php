<?php

use App\Models\cesi_responsable;
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
        Schema::create('cesi_sesions', function (Blueprint $table) {
            $table->id();
            $table->string('sesion_estado')->nullable();
            $table->timestamp('sesion_inicio')->nullable();
            $table->timestamp('sesion_fin')->nullable();
            $table->string('sesion_usuario')->nullable();
            $table->foreignId('cesi_responsable_id')->constrained()->cascadeOnDelete();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cesi_sesions');
    }
};
