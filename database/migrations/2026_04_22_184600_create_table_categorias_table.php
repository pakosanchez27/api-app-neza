<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('event_categories')) {
            Schema::create('event_categories', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 100);
                $table->string('slug', 120)->unique(); // Indispensable para el SEO de tu PWA
                $table->text('descripcion')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('event_categories');
    }
};
