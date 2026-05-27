<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriasPuntosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $categorias = [
            'Zonas de interés',
            'Mercado',
            'Iglesia',
            'Clínica',
            'Hospital',
            'Bomberos',
            'Policia',
            'Protección Civil',
            'Seguridad Ciudadana',
            'Seguridad Pública',
            'Establecimiento',
        ];

        foreach ($categorias as $tipo) {
            $existe = DB::table('categorias_puntos')
                ->where('tipo', $tipo)
                ->exists();

            if ($existe) {
                DB::table('categorias_puntos')
                    ->where('tipo', $tipo)
                    ->update(['updated_at' => $now]);

                continue;
            }

            DB::table('categorias_puntos')->insert([
                'tipo' => $tipo,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
