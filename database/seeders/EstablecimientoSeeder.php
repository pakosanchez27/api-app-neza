<?php

namespace Database\Seeders;

use App\Models\Amenidad;
use App\Models\Contacto;
use App\Models\Domicilio;
use App\Models\Establecimiento;
use App\Models\HorarioEstablecimiento;
use App\Models\User;
use Illuminate\Database\Seeder;

class EstablecimientoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $establecimientos = [
            [
                'user_email' => 'admincomercios1@appturismo.test',
                'data' => [
                    'nombre_est' => 'Cafe Coyotl',
                    'menu' => 'menus/cafe-coyotl.pdf',
                    'aforo' => 45,
                    'logo' => 'logos/cafe-coyotl.png',
                    'id_tipo' => 3,
                    'is_route' => false,
                    'estatus' => true,
                    'razon_social' => 'Cafe Coyotl SA de CV',
                ],
                'contacto' => [
                    'telefono' => '5551001001',
                    'tiktok' => '@cafecoyotl',
                    'instagram' => '@cafecoyotl',
                    'facebook' => 'Cafe Coyotl',
                    'correo' => 'contacto@cafecoyotl.test',
                ],
                'domicilio' => [
                    'calle' => 'Avenida Pantitlan',
                    'colonia' => 'La Perla',
                    'num_int' => null,
                    'num_ext' => '120',
                    'x' => -98.990120,
                    'y' => 19.399820,
                    'localidad' => 'Nezahualcoyotl',
                    'cp' => '57820',
                    'latitud' => 19.399820,
                    'longitud' => -98.990120,
                    'referencias' => 'Frente al mercado de La Perla',
                ],
                'horarios' => [
                    ['dia_semana' => 1, 'hora_apertura' => '08:00:00', 'hora_cierra' => '19:00:00', 'cerrado' => false],
                    ['dia_semana' => 2, 'hora_apertura' => '08:00:00', 'hora_cierra' => '19:00:00', 'cerrado' => false],
                    ['dia_semana' => 3, 'hora_apertura' => '08:00:00', 'hora_cierra' => '19:00:00', 'cerrado' => false],
                    ['dia_semana' => 4, 'hora_apertura' => '08:00:00', 'hora_cierra' => '19:00:00', 'cerrado' => false],
                    ['dia_semana' => 5, 'hora_apertura' => '08:00:00', 'hora_cierra' => '20:00:00', 'cerrado' => false],
                    ['dia_semana' => 6, 'hora_apertura' => '09:00:00', 'hora_cierra' => '20:00:00', 'cerrado' => false],
                    ['dia_semana' => 7, 'hora_apertura' => '09:00:00', 'hora_cierra' => '17:00:00', 'cerrado' => false],
                ],
                'amenidades' => [
                    'Wi-Fi de alta velocidad',
                    'Contactos y puertos USB',
                    'Libros y juegos de mesa',
                    'Música ambiental',
                ],
            ],
            [
                'user_email' => 'admincomercios2@appturismo.test',
                'data' => [
                    'nombre_est' => 'Taqueria La Ruta',
                    'menu' => 'menus/taqueria-la-ruta.pdf',
                    'aforo' => 60,
                    'logo' => 'logos/taqueria-la-ruta.png',
                    'id_tipo' => 1,
                    'is_route' => true,
                    'estatus' => true,
                    'razon_social' => 'Taqueria La Ruta',
                ],
                'contacto' => [
                    'telefono' => '5551001002',
                    'tiktok' => '@taquerialaruta',
                    'instagram' => '@taquerialaruta',
                    'facebook' => 'Taqueria La Ruta',
                    'correo' => 'hola@taquerialaruta.test',
                ],
                'domicilio' => [
                    'calle' => 'Avenida Chimalhuacan',
                    'colonia' => 'Agua Azul',
                    'num_int' => null,
                    'num_ext' => '88',
                    'x' => -98.985500,
                    'y' => 19.410260,
                    'localidad' => 'Nezahualcoyotl',
                    'cp' => '57500',
                    'latitud' => 19.410260,
                    'longitud' => -98.985500,
                    'referencias' => 'A media cuadra de la estacion Mexibus',
                ],
                'horarios' => [
                    ['dia_semana' => 1, 'hora_apertura' => '13:00:00', 'hora_cierra' => '23:00:00', 'cerrado' => false],
                    ['dia_semana' => 2, 'hora_apertura' => '13:00:00', 'hora_cierra' => '23:00:00', 'cerrado' => false],
                    ['dia_semana' => 3, 'hora_apertura' => '13:00:00', 'hora_cierra' => '23:00:00', 'cerrado' => false],
                    ['dia_semana' => 4, 'hora_apertura' => '13:00:00', 'hora_cierra' => '23:00:00', 'cerrado' => false],
                    ['dia_semana' => 5, 'hora_apertura' => '13:00:00', 'hora_cierra' => '00:00:00', 'cerrado' => false],
                    ['dia_semana' => 6, 'hora_apertura' => '13:00:00', 'hora_cierra' => '00:00:00', 'cerrado' => false],
                    ['dia_semana' => 7, 'hora_apertura' => '13:00:00', 'hora_cierra' => '21:00:00', 'cerrado' => false],
                ],
                'amenidades' => [
                    'Estacionamiento / Valet parking',
                    'Aire acondicionado / Calefacción',
                    'Área exclusiva de Pick-up',
                ],
            ],
            [
                'user_email' => 'artesanos@appturismo.test',
                'data' => [
                    'nombre_est' => 'Dulces Artesanales Neza',
                    'menu' => 'menus/dulces-artesanales-neza.pdf',
                    'aforo' => 20,
                    'logo' => 'logos/dulces-artesanales-neza.png',
                    'id_tipo' => 10,
                    'is_route' => false,
                    'estatus' => true,
                    'razon_social' => 'Artesanias y Dulces Rosa Aguilar',
                ],
                'contacto' => [
                    'telefono' => '5551001003',
                    'tiktok' => '@dulcesneza',
                    'instagram' => '@dulcesneza',
                    'facebook' => 'Dulces Artesanales Neza',
                    'correo' => 'ventas@dulcesneza.test',
                ],
                'domicilio' => [
                    'calle' => 'Calle Cielito Lindo',
                    'colonia' => 'Benito Juarez',
                    'num_int' => '2',
                    'num_ext' => '15',
                    'x' => -98.997700,
                    'y' => 19.404100,
                    'localidad' => 'Nezahualcoyotl',
                    'cp' => '57000',
                    'latitud' => 19.404100,
                    'longitud' => -98.997700,
                    'referencias' => 'Interior del corredor comercial artesanal',
                ],
                'horarios' => [
                    ['dia_semana' => 1, 'hora_apertura' => '10:00:00', 'hora_cierra' => '18:00:00', 'cerrado' => false],
                    ['dia_semana' => 2, 'hora_apertura' => '10:00:00', 'hora_cierra' => '18:00:00', 'cerrado' => false],
                    ['dia_semana' => 3, 'hora_apertura' => '10:00:00', 'hora_cierra' => '18:00:00', 'cerrado' => false],
                    ['dia_semana' => 4, 'hora_apertura' => '10:00:00', 'hora_cierra' => '18:00:00', 'cerrado' => false],
                    ['dia_semana' => 5, 'hora_apertura' => '10:00:00', 'hora_cierra' => '18:00:00', 'cerrado' => false],
                    ['dia_semana' => 6, 'hora_apertura' => '11:00:00', 'hora_cierra' => '17:00:00', 'cerrado' => false],
                    ['dia_semana' => 7, 'hora_apertura' => null, 'hora_cierra' => null, 'cerrado' => true],
                ],
                'amenidades' => [
                    'Agua de cortesía (Self-service)',
                    'Estacionamiento para bicicletas',
                    'Mobiliario ergonómico',
                ],
            ],
        ];

        foreach ($establecimientos as $seed) {
            $user = User::query()->where('email', $seed['user_email'])->first();

            if (!$user) {
                continue;
            }

            $establecimiento = Establecimiento::query()->updateOrCreate(
                ['nombre_est' => $seed['data']['nombre_est']],
                array_merge($seed['data'], ['user_id' => $user->id])
            );

            Contacto::query()->updateOrCreate(
                ['id_establecimiento' => $establecimiento->id_establecimiento],
                array_merge($seed['contacto'], ['id_establecimiento' => $establecimiento->id_establecimiento])
            );

            Domicilio::query()->updateOrCreate(
                ['id_establecimiento' => $establecimiento->id_establecimiento],
                array_merge($seed['domicilio'], ['id_establecimiento' => $establecimiento->id_establecimiento])
            );

            HorarioEstablecimiento::query()
                ->where('id_establecimiento', $establecimiento->id_establecimiento)
                ->delete();

            foreach ($seed['horarios'] as $horario) {
                HorarioEstablecimiento::query()->create(
                    array_merge($horario, ['id_establecimiento' => $establecimiento->id_establecimiento])
                );
            }

            $amenidadIds = Amenidad::query()
                ->whereIn('nombre', $seed['amenidades'])
                ->pluck('id_amenidades')
                ->all();

            $establecimiento->amenidades()->sync($amenidadIds);
        }
    }
}
