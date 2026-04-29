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
        Schema::create('est_amenidades', function (Blueprint $table) {
            $table->increments('id_est_amenidades');
            $table->unsignedInteger('id_amenidades');
            $table->unsignedInteger('id_establecimiento');

            $table->unique(['id_amenidades', 'id_establecimiento'], 'unique_est_amenidades');

            $table->foreign('id_amenidades')
                ->references('id_amenidades')
                ->on('amenidades')
                ->cascadeOnDelete()
                ->noActionOnUpdate();

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
        Schema::dropIfExists('est_amenidades');
    }
};
