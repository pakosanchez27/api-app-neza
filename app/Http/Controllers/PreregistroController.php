<?php

namespace App\Http\Controllers;

use App\Mail\PreregistroReceivedMail;
use App\Http\Requests\StorePublicPreregistroRequest;
use App\Models\Preregistro;
use App\Support\ImageManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PreregistroController extends Controller
{
    public function store(StorePublicPreregistroRequest $request): JsonResponse
    {
        $data = $request->validated();

        $preregistro = DB::transaction(function () use ($request, $data) {
            $storageDirectory = $this->buildStorageDirectory($data['nombre_est']);

            return Preregistro::query()->create([
                'nombre_p' => $data['nombre_p'],
                'app_p' => $data['app_p'],
                'apm_p' => $data['apm_p'] ?? null,
                'razon_social' => $data['razon_social'],
                'telefono' => $data['telefono'],
                'correo' => $data['correo'],
                'nombre_est' => $data['nombre_est'],
                'tipo' => (int) $data['tipo'],
                'descripcion_est' => $data['descripcion_est'],
                'calle' => $data['calle'],
                'numero' => $data['numero'],
                'colonia' => $data['colonia'] ?? null,
                'codigo_postal' => $data['codigo_postal'] ?? null,
                'lic_fun' => $this->storePreregistroFile($request->file('lic_fun'), $storageDirectory),
                'ine' => $this->storePreregistroFile($request->file('ine'), $storageDirectory),
                'latitud' => $data['latitud_us'] ?? null,
                'longitud' => $data['longitud_us'] ?? null,
                'estatus_registro' => Preregistro::ESTATUS_PENDIENTE,
                'observacion_registro' => null,
                'aviso_privacidad' => true,
                'foto_est' => $this->storePreregistroFile($request->file('foto_est'), $storageDirectory),
            ]);
        });

        try {
            Mail::to($preregistro->correo)->send(new PreregistroReceivedMail($preregistro));
        } catch (\Throwable $exception) {
            report($exception);

            Log::warning('No fue posible enviar el correo de confirmacion del preregistro.', [
                'id_preresgistro' => $preregistro->id_preresgistro,
                'correo' => $preregistro->correo,
                'message' => $exception->getMessage(),
            ]);
        }

        return response()->json([
            'message' => 'Tu preregistro fue enviado correctamente.',
            'data' => [
                'id_preresgistro' => $preregistro->id_preresgistro,
                'nombre_est' => $preregistro->nombre_est,
                'correo' => $preregistro->correo,
                'estatus_registro' => $preregistro->estatus_registro,
            ],
        ], 201);
    }

    public function showCorrection(string $token): JsonResponse
    {
        $preregistro = $this->resolveCorrectionPreregistro($token);

        if (! $preregistro) {
            return response()->json([
                'message' => 'El enlace de correccion no es valido o ya vencio.',
            ], 404);
        }

        return response()->json([
            'message' => 'Preregistro cargado correctamente.',
            'data' => [
                'nombre_p' => $preregistro->nombre_p,
                'app_p' => $preregistro->app_p,
                'apm_p' => $preregistro->apm_p,
                'telefono' => $preregistro->telefono,
                'correo' => $preregistro->correo,
                'nombre_est' => $preregistro->nombre_est,
                'razon_social' => $preregistro->razon_social,
                'tipo' => $preregistro->tipo ? (string) $preregistro->tipo : '',
                'descripcion_est' => $preregistro->descripcion_est,
                'calle' => $preregistro->calle,
                'numero' => $preregistro->numero,
                'colonia' => $preregistro->colonia,
                'codigo_postal' => $preregistro->codigo_postal,
                'ubicacion_x' => $preregistro->longitud !== null ? (string) $preregistro->longitud : '',
                'ubicacion_y' => $preregistro->latitud !== null ? (string) $preregistro->latitud : '',
                'latitud_us' => $preregistro->latitud !== null ? (string) $preregistro->latitud : '',
                'longitud_us' => $preregistro->longitud !== null ? (string) $preregistro->longitud : '',
                'aviso_privacidad' => (bool) $preregistro->aviso_privacidad,
                'observacion_registro' => $preregistro->observacion_registro,
                'tiene_ine' => ! empty($preregistro->ine),
                'tiene_lic_fun' => ! empty($preregistro->lic_fun),
                'tiene_foto_est' => ! empty($preregistro->foto_est),
            ],
        ]);
    }

    public function updateCorrection(StorePublicPreregistroRequest $request, string $token): JsonResponse
    {
        $preregistro = $this->resolveCorrectionPreregistro($token);

        if (! $preregistro) {
            return response()->json([
                'message' => 'El enlace de correccion no es valido o ya vencio.',
            ], 404);
        }

        $data = $request->validated();

        DB::transaction(function () use ($request, $data, $preregistro) {
            $storageDirectory = $this->buildStorageDirectory($data['nombre_est']);

            $preregistro->update([
                'nombre_p' => $data['nombre_p'],
                'app_p' => $data['app_p'],
                'apm_p' => $data['apm_p'] ?? null,
                'razon_social' => $data['razon_social'],
                'telefono' => $data['telefono'],
                'correo' => $data['correo'],
                'nombre_est' => $data['nombre_est'],
                'tipo' => (int) $data['tipo'],
                'descripcion_est' => $data['descripcion_est'],
                'calle' => $data['calle'],
                'numero' => $data['numero'],
                'colonia' => $data['colonia'] ?? null,
                'codigo_postal' => $data['codigo_postal'] ?? null,
                'lic_fun' => $this->replacePreregistroFile($request->file('lic_fun'), $preregistro->lic_fun, $storageDirectory),
                'ine' => $this->replacePreregistroFile($request->file('ine'), $preregistro->ine, $storageDirectory),
                'foto_est' => $this->replacePreregistroFile($request->file('foto_est'), $preregistro->foto_est, $storageDirectory),
                'latitud' => $data['latitud_us'] ?? null,
                'longitud' => $data['longitud_us'] ?? null,
                'estatus_registro' => Preregistro::ESTATUS_PENDIENTE,
                'observacion_registro' => null,
                'token_correccion' => null,
                'token_correccion_expira_en' => null,
                'aviso_privacidad' => true,
            ]);
        });

        try {
            Mail::to($preregistro->correo)->send(new PreregistroReceivedMail($preregistro->fresh()));
        } catch (\Throwable $exception) {
            report($exception);

            Log::warning('No fue posible enviar el correo de confirmacion despues de corregir el preregistro.', [
                'id_preresgistro' => $preregistro->id_preresgistro,
                'correo' => $preregistro->correo,
                'message' => $exception->getMessage(),
            ]);
        }

        return response()->json([
            'message' => 'Tu preregistro corregido fue enviado correctamente.',
            'data' => [
                'id_preresgistro' => $preregistro->id_preresgistro,
                'nombre_est' => $preregistro->nombre_est,
                'correo' => $preregistro->correo,
                'estatus_registro' => Preregistro::ESTATUS_PENDIENTE,
            ],
        ]);
    }

    private function storePreregistroFile(?UploadedFile $file, string $directory): ?string
    {
        if (! $file) {
            return null;
        }

        return ImageManager::storePublicDiskFile($file, $directory);
    }

    private function buildStorageDirectory(string $establishmentName): string
    {
        $folderName = Str::slug($establishmentName, '-');

        if ($folderName === '') {
            $folderName = 'establecimiento';
        }

        return $folderName;
    }

    private function replacePreregistroFile(?UploadedFile $file, ?string $existingPath, string $directory): ?string
    {
        if (! $file) {
            return $existingPath;
        }

        if ($existingPath) {
            Storage::disk('public')->delete($existingPath);
        }

        return $this->storePreregistroFile($file, $directory);
    }

    private function resolveCorrectionPreregistro(string $token): ?Preregistro
    {
        $preregistro = Preregistro::query()
            ->where('token_correccion', $token)
            ->first();

        if (! $preregistro || ! $preregistro->puedeCorregirseConToken($token)) {
            return null;
        }

        return $preregistro;
    }
}
