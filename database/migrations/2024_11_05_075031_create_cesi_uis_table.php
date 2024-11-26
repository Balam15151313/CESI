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
        Schema::create('cesi_uis', function (Blueprint $table) {
            $table->id();
            $table->string('ui_color1')->nullable();
            $table->string('ui_color2')->nullable();
            $table->string('ui_color3')->nullable();
            $table->foreignId('cesi_escuela_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cesi_uis');
    }
};
