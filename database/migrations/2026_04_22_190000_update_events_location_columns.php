<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('events')) {
            return;
        }

        Schema::table('events', function (Blueprint $table) {
            if (! Schema::hasColumn('events', 'calle')) {
                $table->string('calle')->nullable()->after('hora');
            }

            if (! Schema::hasColumn('events', 'numero')) {
                $table->string('numero', 30)->nullable()->after('calle');
            }

            if (! Schema::hasColumn('events', 'colonia')) {
                $table->string('colonia')->nullable()->after('numero');
            }
        });

        if (Schema::hasColumn('events', 'ubicacion')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('ubicacion');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('events')) {
            return;
        }

        Schema::table('events', function (Blueprint $table) {
            if (! Schema::hasColumn('events', 'ubicacion')) {
                $table->string('ubicacion')->nullable()->after('hora');
            }
        });

        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'colonia')) {
                $table->dropColumn('colonia');
            }

            if (Schema::hasColumn('events', 'numero')) {
                $table->dropColumn('numero');
            }

            if (Schema::hasColumn('events', 'calle')) {
                $table->dropColumn('calle');
            }
        });
    }
};
