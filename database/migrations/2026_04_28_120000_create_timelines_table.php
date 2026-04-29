<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('timelines')) {
            return;
        }

        Schema::create('timelines', function (Blueprint $table) {
            $table->id();
            $table->string('lugar_turistico', 255);
            $table->text('descripcion')->nullable();
            $table->string('imagen_antes')->nullable();
            $table->string('imagen_despues')->nullable();
            $table->unsignedInteger('orden')->default(0);
            $table->tinyInteger('estatus')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timelines');
    }
};
