<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('historia_galeria')) {
            return;
        }

        Schema::create('historia_galeria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historia_id')->constrained('historias')->onDelete('cascade');
            $table->string('imagen', 255);
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historia_galeria');
    }
};
