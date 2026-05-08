<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pasaportes_usuario', function (Blueprint $table) {
            $table->increments('id_pasaporte');
            $table->foreignId('user_id');
            $table->unsignedInteger('id_ruta');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->unique(['user_id', 'id_ruta'], 'unique_user_ruta_pasaporte');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete()
                ->noActionOnUpdate();

            $table->foreign('id_ruta')
                ->references('id_ruta')
                ->on('rutas')
                ->cascadeOnDelete()
                ->noActionOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pasaportes_usuario');
    }
};
