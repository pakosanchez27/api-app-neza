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
        Schema::create('puntos_mapa', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_punto', 150);
            $table->text('descripcion')->nullable();
            $table->string('foto_principal', 255)->nullable();

            $table->foreignId('categoria_id')
                ->constrained('categorias_puntos')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->string('calle', 150)->nullable();
            $table->string('numero_exterior', 20)->nullable();
            $table->string('numero_interior', 20)->nullable();
            $table->string('cp', 10)->nullable();
            $table->string('colonia', 150)->nullable();

            $table->decimal('latitud', 10, 8);
            $table->decimal('longitud', 11, 8);

            $table->string('telefono', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('horarios')->nullable();
            $table->unsignedTinyInteger('estatus')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puntos_mapa');
    }
};
