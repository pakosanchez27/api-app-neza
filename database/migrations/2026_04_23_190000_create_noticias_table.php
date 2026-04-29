<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('noticias')) {
            return;
        }

        Schema::create('noticias', function (Blueprint $table) {
            $table->id();
            $table->string('portada')->nullable();
            $table->string('titulo', 255);
            $table->string('subtitulo', 255)->nullable();
            $table->text('resumen')->nullable();
            $table->json('galeria')->nullable();
            $table->string('cta')->nullable();
            $table->date('fecha_publicacion');
            $table->tinyInteger('estatus')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('noticias');
    }
};
