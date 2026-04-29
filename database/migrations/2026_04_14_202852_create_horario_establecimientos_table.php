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
        Schema::create('horario_establecimientos', function (Blueprint $table) {
            $table->increments('id_horario');
            $table->unsignedTinyInteger('dia_semana');
            $table->time('hora_apertura')->nullable();
            $table->time('hora_cierra')->nullable();
            $table->boolean('cerrado')->nullable()->default(false);
            $table->unsignedInteger('id_establecimiento');

            $table->foreign('id_establecimiento')
                ->references('id_establecimiento')
                ->on('establecimientos')
                ->cascadeOnDelete()
                ->noActionOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horario_establecimientos');
    }
};
