<?php

namespace Database\Seeders;

use App\Models\TipoDocumento;
use Illuminate\Database\Seeder;

class TipoDocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiposDocumento = [
            ['nombre' => 'ine', 'descripcion' => 'Identificación oficial del usuario o responsable.'],
            ['nombre' => 'licencia de funcionamiento', 'descripcion' => 'Documento que acredita el funcionamiento del establecimiento.'],
            ['nombre' => 'logo', 'descripcion' => 'Archivo de identidad visual del establecimiento.'],
            ['nombre' => 'menu', 'descripcion' => 'Archivo del menú vigente del establecimiento.'],
        ];

        foreach ($tiposDocumento as $tipoDocumento) {
            TipoDocumento::query()->updateOrCreate(
                ['nombre' => $tipoDocumento['nombre']],
                ['descripcion' => $tipoDocumento['descripcion']]
            );
        }
    }
}
