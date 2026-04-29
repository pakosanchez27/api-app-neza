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
        Schema::create('establecimientos', function (Blueprint $table) {
            $table->increments('id_establecimiento');
            $table->string('nombre_est', 50)->nullable();
            $table->string('menu', 255)->nullable();
            $table->integer('aforo')->nullable();
            $table->string('logo', 255)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->foreignId('user_id');
            $table->unsignedInteger('id_tipo');
            $table->boolean('estatus')->nullable()->default(false);
            $table->string('razon_social', 200)->nullable();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->noActionOnDelete()
                ->noActionOnUpdate();

            $table->foreign('id_tipo')
                ->references('id_tipo')
                ->on('tipos')
                ->noActionOnDelete()
                ->noActionOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('establecimientos');
    }
};
