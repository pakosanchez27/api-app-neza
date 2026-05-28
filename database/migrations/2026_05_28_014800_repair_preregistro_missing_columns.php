<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('preregistro')) {
            return;
        }

        $columnsToAdd = [
            'calle' => fn (Blueprint $table) => $table->string('calle', 150)->nullable()->after('descripcion_est'),
            'numero' => fn (Blueprint $table) => $table->string('numero', 30)->nullable()->after('calle'),
            'colonia' => fn (Blueprint $table) => $table->string('colonia', 150)->nullable()->after('numero'),
            'codigo_postal' => fn (Blueprint $table) => $table->string('codigo_postal', 10)->nullable()->after('colonia'),
            'token_correccion' => fn (Blueprint $table) => $table->string('token_correccion', 120)->nullable()->after('foto_est'),
            'token_correccion_expira_en' => fn (Blueprint $table) => $table->timestamp('token_correccion_expira_en')->nullable()->after('token_correccion'),
        ];

        foreach ($columnsToAdd as $column => $definition) {
            if (Schema::hasColumn('preregistro', $column)) {
                continue;
            }

            Schema::table('preregistro', function (Blueprint $table) use ($definition) {
                $definition($table);
            });
        }

        $hasTokenColumn = Schema::hasColumn('preregistro', 'token_correccion');
        $hasTokenUnique = collect(Schema::getIndexes('preregistro'))
            ->contains(fn (array $index) => ($index['name'] ?? null) === 'preregistro_token_correccion_unique');

        if ($hasTokenColumn && ! $hasTokenUnique) {
            Schema::table('preregistro', function (Blueprint $table) {
                $table->unique('token_correccion', 'preregistro_token_correccion_unique');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('preregistro')) {
            return;
        }

        $hasTokenUnique = collect(Schema::getIndexes('preregistro'))
            ->contains(fn (array $index) => ($index['name'] ?? null) === 'preregistro_token_correccion_unique');

        if ($hasTokenUnique) {
            Schema::table('preregistro', function (Blueprint $table) {
                $table->dropUnique('preregistro_token_correccion_unique');
            });
        }

        foreach (['token_correccion_expira_en', 'token_correccion', 'codigo_postal', 'colonia', 'numero', 'calle'] as $column) {
            if (! Schema::hasColumn('preregistro', $column)) {
                continue;
            }

            Schema::table('preregistro', function (Blueprint $table) use ($column) {
                $table->dropColumn($column);
            });
        }
    }
};
