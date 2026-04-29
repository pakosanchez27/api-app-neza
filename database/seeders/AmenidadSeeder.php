<?php

namespace Database\Seeders;

use App\Models\Amenidad;
use Illuminate\Database\Seeder;

class AmenidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amenidades = [
            'Wi-Fi de alta velocidad',
            'Contactos y puertos USB',
            'Estacionamiento / Valet parking',
            'Zona Pet Friendly',
            'Terraza o área de fumadores',
            'Aire acondicionado / Calefacción',
            'Área infantil (Kids zone)',
            'Agua de cortesía (Self-service)',
            'Estaciones de carga para móviles',
            'Cambiadores para bebés en baños',
            'Libros y juegos de mesa',
            'Música ambiental',
            'Área exclusiva de Pick-up',
            'Estacionamiento para bicicletas',
            'Mobiliario ergonómico',
        ];

        foreach ($amenidades as $nombre) {
            Amenidad::query()->updateOrCreate(
                ['nombre' => $nombre],
                ['descripcion' => null]
            );
        }
    }
}
