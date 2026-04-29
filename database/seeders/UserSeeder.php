<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'nombre_p' => 'Super',
                'app_p' => 'Admin',
                'apm_p' => 'Neza',
                'email' => 'superadmin@appturismo.test',
                'foto_perfil' => 'https://cdn-icons-png.flaticon.com/512/1077/1077114.png',
                'telefono' => '5510000001',
                'password' => 'Password123!',
                'ultimo_acceso' => Carbon::today(),
                'estatus' => 'aprobado',
                'activo' => true,
                'id_rol' => 1,
            ],
            [
                'name' => 'Laura Martinez',
                'nombre_p' => 'Laura',
                'app_p' => 'Martinez',
                'apm_p' => 'Rios',
                'email' => 'admincomercios1@appturismo.test',
                'telefono' => '5510000002',
                'password' => 'Password123!',
                'ultimo_acceso' => Carbon::today(),
                'estatus' => 'aprobado',
                'activo' => true,
                'id_rol' => 2,
            ],
            [
                'name' => 'Diego Hernandez',
                'nombre_p' => 'Diego',
                'app_p' => 'Hernandez',
                'apm_p' => 'Lopez',
                'email' => 'admincomercios2@appturismo.test',
                'telefono' => '5510000003',
                'password' => 'Password123!',
                'ultimo_acceso' => Carbon::today(),
                'estatus' => 'aprobado',
                'activo' => true,
                'id_rol' => 2,
            ],
            [
                'name' => 'Andrea Gomez',
                'nombre_p' => 'Andrea',
                'app_p' => 'Gomez',
                'apm_p' => 'Soto',
                'email' => 'usuario@appturismo.test',
                'telefono' => '5510000004',
                'password' => 'Password123!',
                'ultimo_acceso' => Carbon::today(),
                'estatus' => 'activo',
                'activo' => true,
                'id_rol' => 3,
            ],
            [
                'name' => 'Rosa Aguilar',
                'nombre_p' => 'Rosa',
                'app_p' => 'Aguilar',
                'apm_p' => 'Vega',
                'email' => 'artesanos@appturismo.test',
                'telefono' => '5510000005',
                'password' => 'Password123!',
                'ultimo_acceso' => Carbon::today(),
                'estatus' => 'activo',
                'activo' => true,
                'id_rol' => 4,
            ],
        ];

        foreach ($users as $userData) {
            User::query()->updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
