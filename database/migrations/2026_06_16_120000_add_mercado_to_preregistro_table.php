<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('preregistro', 'mercado')) {
            Schema::table('preregistro', function (Blueprint $table) {
                $table->string('mercado', 150)->nullable()->after('codigo_postal');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('preregistro', 'mercado')) {
            Schema::table('preregistro', function (Blueprint $table) {
                $table->dropColumn('mercado');
            });
        }
    }
};
