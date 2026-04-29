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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('id_rol')->nullable()->after('foto_perfil');

            $table->foreign('id_rol', 'users_id_rol_foreign')
                ->references('id_rol')
                ->on('roles')
                ->noActionOnDelete()
                ->noActionOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_id_rol_foreign');
            $table->dropColumn('id_rol');
        });
    }
};
