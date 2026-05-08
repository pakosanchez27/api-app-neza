<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $routeId = DB::table('rutas')->where('slug', 'ruta-gastronomica-neza')->value('id_ruta');

        if (!$routeId) {
            $routeId = DB::table('rutas')->insertGetId([
                'nombre' => 'Ruta Gastronomica Neza',
                'slug' => 'ruta-gastronomica-neza',
                'descripcion' => 'Recorrido gastronomico principal de NezaGo.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ], 'id_ruta');
        }

        $establecimientos = DB::table('establecimientos')
            ->where('is_route', true)
            ->orderBy('id_establecimiento')
            ->get(['id_establecimiento']);

        $orden = 1;

        foreach ($establecimientos as $establecimiento) {
            DB::table('ruta_establecimiento')->updateOrInsert(
                [
                    'id_ruta' => $routeId,
                    'id_establecimiento' => $establecimiento->id_establecimiento,
                ],
                [
                    'orden' => $orden,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            $orden++;
        }
    }

    public function down(): void
    {
        $routeId = DB::table('rutas')->where('slug', 'ruta-gastronomica-neza')->value('id_ruta');

        if (!$routeId) {
            return;
        }

        DB::table('ruta_establecimiento')->where('id_ruta', $routeId)->delete();
        DB::table('rutas')->where('id_ruta', $routeId)->delete();
    }
};
