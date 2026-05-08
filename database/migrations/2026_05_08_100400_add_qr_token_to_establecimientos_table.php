<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('establecimientos', 'qr_token')) {
            Schema::table('establecimientos', function (Blueprint $table) {
                $table->string('qr_token', 120)->nullable()->after('is_visible');
            });
        }

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE establecimientos MODIFY qr_token VARCHAR(120) NULL');
        }

        $establecimientos = DB::table('establecimientos')
            ->select('id_establecimiento', 'qr_token')
            ->get();

        foreach ($establecimientos as $establecimiento) {
            if (!empty($establecimiento->qr_token)) {
                continue;
            }

            DB::table('establecimientos')
                ->where('id_establecimiento', $establecimiento->id_establecimiento)
                ->update([
                    'qr_token' => sprintf(
                        'NEZA-QR-%s-%s',
                        $establecimiento->id_establecimiento,
                        Str::upper(Str::random(12))
                    ),
                ]);
        }

        Schema::table('establecimientos', function (Blueprint $table) {
            $table->unique('qr_token', 'unique_establecimientos_qr_token');
        });
    }

    public function down(): void
    {
        Schema::table('establecimientos', function (Blueprint $table) {
            $table->dropUnique('unique_establecimientos_qr_token');
            $table->dropColumn('qr_token');
        });
    }
};
