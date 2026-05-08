<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ruta_establecimiento', function (Blueprint $table) {
            $table->increments('id_ruta_establecimiento');
            $table->unsignedInteger('id_ruta');
            $table->unsignedInteger('id_establecimiento');
            $table->unsignedInteger('orden')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->unique(['id_ruta', 'id_establecimiento'], 'unique_ruta_establecimiento');

            $table->foreign('id_ruta')
                ->references('id_ruta')
                ->on('rutas')
                ->cascadeOnDelete()
                ->noActionOnUpdate();

            $table->foreign('id_establecimiento')
                ->references('id_establecimiento')
                ->on('establecimientos')
                ->cascadeOnDelete()
                ->noActionOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruta_establecimiento');
    }
};
