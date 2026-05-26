<?php

namespace App\Http\Controllers;

use App\Mail\PreregistroReceivedMail;
use App\Http\Requests\StorePublicPreregistroRequest;
use App\Models\Preregistro;
use App\Support\ImageManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
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
                'lic_fun' => $this->storePreregistroFile($request->file('lic_fun'), $storageDirectory),
                'ine' => $this->storePreregistroFile($request->file('ine'), $storageDirectory),
                'latitud' => $data['latitud_us'] ?? null,
                'longitud' => $data['longitud_us'] ?? null,
                'estatus_registro' => 0,
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
}
