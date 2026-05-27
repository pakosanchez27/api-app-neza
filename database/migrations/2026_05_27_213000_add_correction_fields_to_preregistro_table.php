<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('preregistro', function (Blueprint $table) {
            if (! Schema::hasColumn('preregistro', 'calle')) {
                $table->string('calle', 150)->nullable()->after('descripcion_est');
            }

            if (! Schema::hasColumn('preregistro', 'numero')) {
                $table->string('numero', 30)->nullable()->after('calle');
            }

            if (! Schema::hasColumn('preregistro', 'colonia')) {
                $table->string('colonia', 150)->nullable()->after('numero');
            }

            if (! Schema::hasColumn('preregistro', 'codigo_postal')) {
                $table->string('codigo_postal', 10)->nullable()->after('colonia');
            }

            if (! Schema::hasColumn('preregistro', 'token_correccion')) {
                $table->string('token_correccion', 120)->nullable()->after('foto_est');
                $table->unique('token_correccion', 'preregistro_token_correccion_unique');
            }

            if (! Schema::hasColumn('preregistro', 'token_correccion_expira_en')) {
                $table->timestamp('token_correccion_expira_en')->nullable()->after('token_correccion');
            }
        });
    }

    public function down(): void
    {
        Schema::table('preregistro', function (Blueprint $table) {
            if (Schema::hasColumn('preregistro', 'token_correccion')) {
                $table->dropUnique('preregistro_token_correccion_unique');
                $table->dropColumn('token_correccion');
            }

            if (Schema::hasColumn('preregistro', 'token_correccion_expira_en')) {
                $table->dropColumn('token_correccion_expira_en');
            }

            foreach (['codigo_postal', 'colonia', 'numero', 'calle'] as $column) {
                if (Schema::hasColumn('preregistro', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
