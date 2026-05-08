<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pasaporte_sellos', function (Blueprint $table) {
            $table->increments('id_pasaporte_sello');
            $table->unsignedInteger('id_pasaporte');
            $table->unsignedInteger('id_establecimiento');
            $table->string('qr_token_usado', 255)->nullable();
            $table->timestamp('sealed_at')->nullable()->useCurrent();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->unique(['id_pasaporte', 'id_establecimiento'], 'unique_pasaporte_establecimiento');

            $table->foreign('id_pasaporte')
                ->references('id_pasaporte')
                ->on('pasaportes_usuario')
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
        Schema::dropIfExists('pasaporte_sellos');
    }
};
