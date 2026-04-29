<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('events')) {
            Schema::create('events', function (Blueprint $table) {
                $table->id();
                $table->string('titulo');
                $table->string('portada')->nullable();
                $table->date('fecha');
                $table->time('hora');
                $table->string('calle');
                $table->string('numero', 30);
                $table->string('colonia');
                $table->string('latitud')->nullable();
                $table->string('longitud')->nullable();
                $table->text('acerca')->nullable();
                $table->boolean('is_destacado')->default(false);
                $table->tinyInteger('estatus')->default(1);
                $table->foreignId('category_id')->constrained('event_categories')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
