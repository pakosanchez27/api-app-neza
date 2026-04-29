<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            TipoSeeder::class,
            AmenidadSeeder::class,
            TipoDocumentoSeeder::class,
            // UserSeeder::class,
            // EstablecimientoSeeder::class,
            CategoriasEventosSeeder::class,
        ]);
    }
}
