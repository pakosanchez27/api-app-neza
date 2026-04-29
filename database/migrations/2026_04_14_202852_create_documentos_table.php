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
        Schema::create('documentos', function (Blueprint $table) {
            $table->increments('id_documento');
            $table->string('nombre_original', 255)->nullable();
            $table->string('nombre_guardado', 255)->nullable();
            $table->string('ruta_archivo', 500)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->unsignedInteger('id_tipo_documento');

            $table->foreign('id_tipo_documento')
                ->references('id_tipo_documento')
                ->on('tipo_documentos')
                ->noActionOnDelete()
                ->noActionOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
