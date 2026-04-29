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
        Schema::create('doc_usuarios', function (Blueprint $table) {
            $table->increments('id_doc_usuarios');
            $table->foreignId('user_id');
            $table->unsignedInteger('id_documento');

            $table->unique(['user_id', 'id_documento'], 'unique_doc_usuarios');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('doc_usuarios');
    }
};
