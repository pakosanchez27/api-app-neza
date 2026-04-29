<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evento_interesados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('events')->onDelete('cascade');
            $table->string('visitor_id', 100);
            $table->timestamps();

            $table->unique(['evento_id', 'visitor_id'], 'unique_evento_visitor');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evento_interesados');
    }
};
