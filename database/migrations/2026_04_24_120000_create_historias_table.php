<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('historias')) {
            return;
        }

        Schema::create('historias', function (Blueprint $table) {
            $table->id();
            $table->string('portada')->nullable();
            $table->string('titulo', 255);
            $table->string('slug', 191)->unique();
            $table->string('autor', 150)->nullable();
            $table->text('resumen_corto')->nullable();
            $table->string('periodo', 255)->nullable();
            $table->longText('desarrollo')->nullable();
            $table->date('fecha_publicacion')->nullable();
            $table->tinyInteger('estatus')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historias');
    }
};
