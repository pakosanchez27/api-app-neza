<?php

namespace Database\Seeders;

use App\Models\EventoCategoriasModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriasEventosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
       public function run(): void
    {
        $categories = [
            'Cultura y Arte',
            'Música y Conciertos',
            'Gastronomía',
            'Deportes',
            'Religioso y Tradición',
            'Cívico y Gubernamental',
            'Educación y Talleres',
            'Infantil y Familiar'
        ];

        foreach ($categories as $nombre) {
            EventoCategoriasModel::create([
                'nombre' => $nombre,
                'slug' => Str::slug($nombre),
                'descripcion' => "Eventos relacionados con $nombre en Nezahualcóyotl."
            ]);
        }
    }

}
