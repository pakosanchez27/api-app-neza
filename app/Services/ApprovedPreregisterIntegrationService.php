<?php

namespace App\Services;

use App\Models\Contacto;
use App\Models\Documento;
use App\Models\Domicilio;
use App\Models\Establecimiento;
use App\Models\Role;
use App\Models\TipoDocumento;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class ApprovedPreregisterIntegrationService
{
    public function ensureAdminComerciosRole(): Role
    {
        $role = Role::query()
            ->where('nombre', 'AdminComercios')
            ->first();

        if (! $role) {
            throw new RuntimeException('No existe el rol AdminComercios.');
        }

        return $role;
    }

    public function createApprovedCommerce(array $payload, bool $isRoute = false, ?string $temporaryPassword = null): array
    {
        $role = $this->ensureAdminComerciosRole();
        $temporaryPassword ??= Str::slug(Str::random(8));

        $result = DB::transaction(function () use ($payload, $role, $temporaryPassword, $isRoute) {
            $solicitante = $payload['solicitante'];
            $establecimientoData = $payload['establecimiento'];
            $ubicacion = $payload['ubicacion'] ?? [];
            $documentos = $payload['documentos'] ?? [];

            $user = User::query()->create([
                'name' => trim(($solicitante['nombre'] ?? '') . ' ' . ($solicitante['apellido_p'] ?? '')),
                'nombre_p' => $solicitante['nombre'],
                'app_p' => $solicitante['apellido_p'],
                'apm_p' => $solicitante['apellido_m'] ?? null,
                'email' => $solicitante['email'],
                'telefono' => $solicitante['telefono'] ?? null,
                'password' => Hash::make($temporaryPassword),
                'is_password_templ' => true,
                'estatus' => 'aprobado',
                'activo' => true,
                'token_activacion' => Str::random(60),
                'id_rol' => $role->id_rol,
            ]);

            $establecimiento = Establecimiento::query()->create([
                'nombre_est' => $establecimientoData['nombre_comercial'],
                'razon_social' => $establecimientoData['razon_social'] ?? null,
                'id_tipo' => $establecimientoData['tipo_id'],
                'descripcion' => $establecimientoData['descripcion'] ?? null,
                'is_route' => $isRoute,
                'user_id' => $user->id,
                'estatus' => false,
            ]);

            Contacto::query()->create([
                'telefono' => $solicitante['telefono'] ?? null,
                'correo' => $solicitante['email'],
                'id_establecimiento' => $establecimiento->id_establecimiento,
            ]);

            Domicilio::query()->create([
                'calle' => $ubicacion['calle'] ?? null,
                'colonia' => $ubicacion['colonia'] ?? null,
                'num_int' => $ubicacion['num_int'] ?? null,
                'num_ext' => $ubicacion['num_ext'] ?? null,
                'x' => $ubicacion['longitud'] ?? null,
                'y' => $ubicacion['latitud'] ?? null,
                'localidad' => $ubicacion['localidad'] ?? null,
                'cp' => $ubicacion['cp'] ?? null,
                'latitud' => $ubicacion['latitud'] ?? null,
                'longitud' => $ubicacion['longitud'] ?? null,
                'referencias' => $ubicacion['referencias'] ?? null,
                'id_establecimiento' => $establecimiento->id_establecimiento,
            ]);

            $documentMap = [
                'ine' => ['type' => 'ine', 'attach_to_user' => true, 'attach_to_establishment' => false],
                'licencia_funcionamiento' => ['type' => 'licencia de funcionamiento', 'attach_to_user' => false, 'attach_to_establishment' => true],
                'foto_establecimiento' => ['type' => 'foto_establecimiento', 'attach_to_user' => false, 'attach_to_establishment' => true],
            ];

            foreach ($documentMap as $payloadKey => $config) {
                $path = $documentos[$payloadKey] ?? null;

                if (! $path) {
                    continue;
                }

                $tipoDocumento = $this->resolveDocumentType($config['type']);

                $documento = Documento::query()->create([
                    'nombre_original' => basename($path),
                    'nombre_guardado' => basename($path),
                    'ruta_archivo' => $path,
                    'id_tipo_documento' => $tipoDocumento->id_tipo_documento,
                ]);

                if ($config['attach_to_user']) {
                    $user->documentos()->syncWithoutDetaching([$documento->id_documento]);
                }

                if ($config['attach_to_establishment']) {
                    $establecimiento->documentos()->syncWithoutDetaching([$documento->id_documento]);
                }
            }

            return compact('user', 'establecimiento');
        });

        return [
            ...$result,
            'temporary_password' => $temporaryPassword,
        ];
    }

    private function resolveDocumentType(string $typeName): TipoDocumento
    {
        return TipoDocumento::query()->firstOrCreate(
            ['nombre' => $typeName],
            ['descripcion' => 'Tipo de documento creado desde integración de preregistros.']
        );
    }
}
