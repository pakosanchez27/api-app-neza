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
        Schema::create('domicilios', function (Blueprint $table) {
            $table->increments('id_domicilio');
            $table->string('calle', 45)->nullable();
            $table->string('colonia', 45)->nullable();
            $table->string('num_int', 45)->nullable();
            $table->string('num_ext', 45)->nullable();
            $table->decimal('x', 20, 6)->nullable();
            $table->decimal('y', 20, 6)->nullable();
            $table->string('localidad', 45)->nullable();
            $table->string('cp', 45)->nullable();
            $table->decimal('latitud', 20, 6)->nullable();
            $table->decimal('longitud', 20, 6)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->unsignedInteger('id_establecimiento');
            $table->string('referencias', 255)->nullable();

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
        Schema::dropIfExists('domicilios');
    }
};
