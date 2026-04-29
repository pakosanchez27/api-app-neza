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
        Schema::create('doc_establecimientos', function (Blueprint $table) {
            $table->increments('id_doc_establecimientos');
            $table->unsignedInteger('id_establecimiento');
            $table->unsignedInteger('id_documento');

            $table->unique(['id_establecimiento', 'id_documento'], 'unique_doc_establecimientos');

            $table->foreign('id_establecimiento')
                ->references('id_establecimiento')
                ->on('establecimientos')
                ->cascadeOnDelete()
                ->noActionOnUpdate();

            $table->foreign('id_documento')
                ->references('id_documento')
                ->on('documentos')
                ->cascadeOnDelete()
                ->noActionOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doc_establecimientos');
    }
};
