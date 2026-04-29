<?php

namespace Database\Seeders;

use App\Models\Tipo;
use Illuminate\Database\Seeder;

class TipoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            ['id_tipo' => 1, 'nombre' => 'Restaurantes'],
            ['id_tipo' => 2, 'nombre' => 'Comida rápida'],
            ['id_tipo' => 3, 'nombre' => 'Cafeterías y bebidas'],
            ['id_tipo' => 4, 'nombre' => 'Panadería y repostería'],
            ['id_tipo' => 5, 'nombre' => 'Venta ambulante o móvil'],
            ['id_tipo' => 6, 'nombre' => 'Comida para llevar'],
            ['id_tipo' => 7, 'nombre' => 'Comercio de alimentos'],
            ['id_tipo' => 8, 'nombre' => 'Bares y bebidas alcohólicas'],
            ['id_tipo' => 9, 'nombre' => 'Servicios de alimentos especializados'],
            ['id_tipo' => 10, 'nombre' => 'Productos especializados'],
        ];

        foreach ($tipos as $tipo) {
            Tipo::query()->updateOrCreate(
                ['id_tipo' => $tipo['id_tipo']],
                ['nombre' => $tipo['nombre']]
            );
        }
    }
}
