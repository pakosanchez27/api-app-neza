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
        Schema::create('contacto', function (Blueprint $table) {
            $table->increments('id_contacto');
            $table->string('telefono', 45)->nullable();
            $table->string('tiktok', 45)->nullable();
            $table->string('instagram', 45)->nullable();
            $table->string('facebook', 45)->nullable();
            $table->string('correo', 45)->nullable();
            $table->unsignedInteger('id_establecimiento');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

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
        Schema::dropIfExists('contacto');
    }
};
