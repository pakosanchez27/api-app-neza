<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['id_rol' => 1, 'nombre' => 'SuperAdmin'],
            ['id_rol' => 2, 'nombre' => 'AdminComercios'],
            ['id_rol' => 3, 'nombre' => 'Usuario'],
            ['id_rol' => 4, 'nombre' => 'Artesanos'],
        ];

        foreach ($roles as $role) {
            Role::query()->updateOrCreate(
                ['id_rol' => $role['id_rol']],
                ['nombre' => $role['nombre']]
            );
        }
    }
}
