<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('establecimientos', function (Blueprint $table) {
            $table->index('is_route');
            $table->index('is_visible');
            $table->index('estatus');
            $table->index(['is_route', 'is_visible', 'estatus'], 'est_route_visible_status_idx');
            $table->index('user_id');
        });

        Schema::table('pasaportes_usuario', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('id_ruta');
        });

        Schema::table('pasaporte_sellos', function (Blueprint $table) {
            $table->index('id_pasaporte');
            $table->index('id_establecimiento');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('telefono');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::table('establecimientos', function (Blueprint $table) {
            $table->dropIndex(['is_route']);
            $table->dropIndex(['is_visible']);
            $table->dropIndex(['estatus']);
            $table->dropIndex('est_route_visible_status_idx');
            $table->dropIndex(['user_id']);
        });

        Schema::table('pasaportes_usuario', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['id_ruta']);
        });

        Schema::table('pasaporte_sellos', function (Blueprint $table) {
            $table->dropIndex(['id_pasaporte']);
            $table->dropIndex(['id_establecimiento']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['telefono']);
            $table->dropIndex(['activo']);
        });
    }
};
