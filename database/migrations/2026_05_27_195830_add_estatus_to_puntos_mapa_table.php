<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('puntos_mapa', 'estatus')) {
            Schema::table('puntos_mapa', function (Blueprint $table) {
                $table->unsignedTinyInteger('estatus')->default(1)->after('horarios');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('puntos_mapa', 'estatus')) {
            Schema::table('puntos_mapa', function (Blueprint $table) {
                $table->dropColumn('estatus');
            });
        }
    }
};
