<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveCommerceRegistrationRequest;
use App\Models\Amenidad;
use App\Models\Documento;
use App\Models\Establecimiento;
use App\Models\HorarioEstablecimiento;
use App\Models\Tipo;
use App\Models\TipoDocumento;
use App\Models\User;
use App\Support\ImageManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CommerceRegistrationController extends Controller
{
    public function save(SaveCommerceRegistrationRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user()->load([
            'establecimientos.contacto',
            'establecimientos.domicilio',
            'establecimientos.horarios',
            'establecimientos.amenidades',
            'establecimientos.documentos.tipoDocumento',
            'establecimientos.tipo',
            'role',
        ]);

        /** @var Establecimiento|null $establecimiento */
        $establecimiento = $user->establecimientos->first();

        if (!$establecimiento) {
            return response()->json([
                'message' => 'No se encontro un establecimiento asociado a esta cuenta.',
            ], 404);
        }

        $phase = (int) $request->validated('phase');
        $payload = $request->validated('payload');
        $finalize = (bool) $request->validated('finalize', false);

        $this->validatePhasePayload($phase, $payload, $request, $user, $establecimiento);

        DB::transaction(function () use ($phase, $payload, $request, $user, $establecimiento, $finalize) {
            match ($phase) {
                1 => $this->saveUserPhase($user, $payload),
                2 => $this->saveBusinessPhase($establecimiento, $payload, $request->file('logo')),
                3 => $this->saveAddressPhase($establecimiento, $payload),
                4 => $this->saveContactPhase($establecimiento, $payload),
                5 => $this->saveSchedulePhase($establecimiento, $payload),
                6 => $this->saveAmenitiesPhase($establecimiento, $payload),
                7 => $this->saveMediaPhase(
                    $establecimiento,
                    $request->file('menu'),
                    $request->file('galeria', [])
                ),
            };

            if ($finalize) {
                $establecimiento->forceFill([
                    'estatus' => true,
                ])->save();
            }
        });

        $user->refresh()->load([
            'role',
            'establecimientos.tipo',
            'establecimientos.contacto',
            'establecimientos.domicilio',
            'establecimientos.horarios',
            'establecimientos.amenidades',
            'establecimientos.documentos.tipoDocumento',
        ]);

        return response()->json([
            'message' => $finalize
                ? 'Registro completado correctamente.'
                : 'Fase guardada correctamente.',
            'phase' => $phase,
            'user' => $user,
        ]);
    }

    private function validatePhasePayload(
        int $phase,
        array $payload,
        SaveCommerceRegistrationRequest $request,
        User $user,
        Establecimiento $establecimiento
    ): void {
        $rules = match ($phase) {
            1 => [
                'currentPassword' => ['nullable', 'string'],
                'newPassword' => ['required', 'string', 'min:8'],
                'confirmPassword' => ['required', 'same:newPassword'],
            ],
            2 => [
                'nombreEstablecimiento' => ['required', 'string', 'max:120'],
                'tipo' => ['required', 'string', 'max:120'],
                'aforo' => ['required', 'integer', 'min:1'],
                'descripcionCorta' => ['required', 'string'],
                'telefonoPrincipal' => ['nullable', 'string', 'min:10', 'max:20'],
            ],
            3 => [
                'calle' => ['required', 'string', 'max:120'],
                'colonia' => ['required', 'string', 'max:120'],
                'numeroInterior' => ['nullable', 'string', 'max:30'],
                'numeroExterior' => ['required', 'string', 'max:30'],
                'localidad' => ['required', 'string', 'max:120'],
                'codigoPostal' => ['required', 'digits:5'],
                'latitud' => ['nullable', 'numeric'],
                'longitud' => ['nullable', 'numeric'],
            ],
            4 => [
                'telefonoNegocio' => ['required', 'string', 'min:10', 'max:20'],
                'correoNegocio' => ['required', 'email', 'max:120'],
                'facebook' => ['nullable', 'string', 'max:255'],
                'instagram' => ['nullable', 'string', 'max:255'],
                'tiktok' => ['nullable', 'string', 'max:255'],
            ],
            5 => [
                'horarios' => ['required', 'array', 'size:7'],
                'horarios.*.closed' => ['required', 'boolean'],
                'horarios.*.open' => ['nullable', 'date_format:H:i'],
                'horarios.*.close' => ['nullable', 'date_format:H:i'],
            ],
            6 => [
                'amenidades' => ['required', 'array'],
                'amenidades.*' => ['required', 'string', 'max:120'],
            ],
            7 => [
                'existingMenu' => ['nullable', 'boolean'],
                'existingGalleryCount' => ['nullable', 'integer', 'min:0'],
            ],
        };

        $validator = Validator::make($payload, $rules);

        $validator->after(function ($validator) use ($phase, $payload, $request, $user, $establecimiento) {
            if ($phase === 1 && !$user->is_password_templ) {
                $currentPassword = $payload['currentPassword'] ?? null;

                if (!$currentPassword) {
                    $validator->errors()->add('currentPassword', 'Debes escribir tu contraseña actual.');
                } elseif (!Hash::check($currentPassword, $user->password ?? '')) {
                    $validator->errors()->add('currentPassword', 'La contraseña actual no es correcta.');
                }
            }

            if ($phase === 5) {
                foreach (($payload['horarios'] ?? []) as $day => $schedule) {
                    $isClosed = (bool) ($schedule['closed'] ?? false);
                    $open = $schedule['open'] ?? null;
                    $close = $schedule['close'] ?? null;

                    if (!$isClosed && (!$open || !$close || $open >= $close)) {
                        $validator->errors()->add(
                            "horarios.$day",
                            'Cada horario abierto debe tener una apertura anterior al cierre.'
                        );
                    }
                }
            }

        });

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    private function saveUserPhase(User $user, array $payload): void
    {
        $user->forceFill([
            'password' => Hash::make($payload['newPassword']),
            'is_password_templ' => false,
        ])->save();
    }

    private function saveBusinessPhase(
        Establecimiento $establecimiento,
        array $payload,
        ?UploadedFile $logoFile
    ): void {
        $tipo = Tipo::query()->firstOrCreate([
            'nombre' => trim($payload['tipo']),
        ]);

        $updates = [
            'nombre_est' => $payload['nombreEstablecimiento'],
            'id_tipo' => $tipo->id_tipo,
            'aforo' => (int) $payload['aforo'],
            'descripcion' => $payload['descripcionCorta'],
        ];

        if ($logoFile) {
            $updates['logo'] = $this->storeFile($logoFile, $establecimiento, 'logo');
        }

        $establecimiento->forceFill($updates)->save();

        if (!empty($payload['telefonoPrincipal'])) {
            $establecimiento->contacto()->updateOrCreate(
                ['id_establecimiento' => $establecimiento->id_establecimiento],
                [
                    'telefono' => preg_replace('/\D+/', '', $payload['telefonoPrincipal']),
                    'correo' => $establecimiento->contacto?->correo,
                    'facebook' => $establecimiento->contacto?->facebook,
                    'instagram' => $establecimiento->contacto?->instagram,
                    'tiktok' => $establecimiento->contacto?->tiktok,
                ]
            );
        }
    }

    private function saveAddressPhase(Establecimiento $establecimiento, array $payload): void
    {
        $establecimiento->domicilio()->updateOrCreate(
            ['id_establecimiento' => $establecimiento->id_establecimiento],
            [
                'calle' => $payload['calle'],
                'colonia' => $payload['colonia'],
                'num_int' => $payload['numeroInterior'] ?? null,
                'num_ext' => $payload['numeroExterior'] ?? null,
                'localidad' => $payload['localidad'],
                'cp' => $payload['codigoPostal'],
                'latitud' => $payload['latitud'] ?? null,
                'longitud' => $payload['longitud'] ?? null,
                'x' => $payload['longitud'] ?? null,
                'y' => $payload['latitud'] ?? null,
            ]
        );
    }

    private function saveContactPhase(Establecimiento $establecimiento, array $payload): void
    {
        $establecimiento->contacto()->updateOrCreate(
            ['id_establecimiento' => $establecimiento->id_establecimiento],
            [
                'telefono' => preg_replace('/\D+/', '', $payload['telefonoNegocio']),
                'correo' => $payload['correoNegocio'],
                'facebook' => $payload['facebook'] ?? null,
                'instagram' => $payload['instagram'] ?? null,
                'tiktok' => $payload['tiktok'] ?? null,
            ]
        );
    }

    private function saveSchedulePhase(Establecimiento $establecimiento, array $payload): void
    {
        $dayNumberByKey = [
            'lunes' => 1,
            'martes' => 2,
            'miercoles' => 3,
            'jueves' => 4,
            'viernes' => 5,
            'sabado' => 6,
            'domingo' => 7,
        ];

        HorarioEstablecimiento::query()
            ->where('id_establecimiento', $establecimiento->id_establecimiento)
            ->delete();

        foreach ($payload['horarios'] as $dayKey => $schedule) {
            HorarioEstablecimiento::query()->create([
                'id_establecimiento' => $establecimiento->id_establecimiento,
                'dia_semana' => $dayNumberByKey[$dayKey] ?? 0,
                'cerrado' => (bool) $schedule['closed'],
                'hora_apertura' => $schedule['closed'] ? null : $schedule['open'],
                'hora_cierra' => $schedule['closed'] ? null : $schedule['close'],
            ]);
        }
    }

    private function saveAmenitiesPhase(Establecimiento $establecimiento, array $payload): void
    {
        $selectedAmenities = collect($payload['amenidades'])
            ->filter(fn ($item) => is_string($item) && trim($item) !== '')
            ->values();

        if ($selectedAmenities->contains(fn (string $item) => mb_strtolower(trim($item)) === 'sin amenidades')) {
            $establecimiento->amenidades()->sync([]);
            return;
        }

        $amenityIds = collect($payload['amenidades'])
            ->filter(fn ($item) => is_string($item) && trim($item) !== '')
            ->map(function (string $amenityName) {
                return Amenidad::query()->firstOrCreate([
                    'nombre' => trim($amenityName),
                ])->id_amenidades;
            })
            ->values()
            ->all();

        $establecimiento->amenidades()->sync($amenityIds);
    }

    private function saveMediaPhase(
        Establecimiento $establecimiento,
        ?UploadedFile $menuFile,
        array $galleryFiles
    ): void {
        if ($menuFile) {
            $this->replaceMenuDocument($establecimiento, $menuFile);
        }

        if (count($galleryFiles) > 0) {
            $this->replaceGalleryDocuments($establecimiento, $galleryFiles);
        }
    }

    private function replaceMenuDocument(Establecimiento $establecimiento, UploadedFile $menuFile): void
    {
        $menuType = $this->resolveDocumentType('menu');
        $path = $this->storeFile($menuFile, $establecimiento, 'menu');

        $existingMenuIds = $establecimiento->documentos()
            ->where('id_tipo_documento', $menuType->id_tipo_documento)
            ->pluck('documentos.id_documento')
            ->all();

        if (count($existingMenuIds) > 0) {
            Documento::query()
                ->whereIn('id_documento', $existingMenuIds)
                ->pluck('ruta_archivo')
                ->filter()
                ->each(fn (string $path) => Storage::disk('public')->delete($path));

            $establecimiento->documentos()->detach($existingMenuIds);
            Documento::query()->whereIn('id_documento', $existingMenuIds)->delete();
        }

        $documento = Documento::query()->create([
            'nombre_original' => $menuFile->getClientOriginalName(),
            'nombre_guardado' => basename($path),
            'ruta_archivo' => $path,
            'id_tipo_documento' => $menuType->id_tipo_documento,
        ]);

        $establecimiento->documentos()->syncWithoutDetaching([$documento->id_documento]);
        $establecimiento->forceFill([
            'menu' => $path,
        ])->save();
    }

    private function replaceGalleryDocuments(Establecimiento $establecimiento, array $galleryFiles): void
    {
        $galleryType = $this->resolveDocumentType('galeria');

        $existingGalleryIds = $establecimiento->documentos()
            ->where('id_tipo_documento', $galleryType->id_tipo_documento)
            ->pluck('documentos.id_documento')
            ->all();

        if (count($existingGalleryIds) > 0) {
            Documento::query()
                ->whereIn('id_documento', $existingGalleryIds)
                ->pluck('ruta_archivo')
                ->filter()
                ->each(fn (string $path) => Storage::disk('public')->delete($path));

            $establecimiento->documentos()->detach($existingGalleryIds);
            Documento::query()->whereIn('id_documento', $existingGalleryIds)->delete();
        }

        foreach ($galleryFiles as $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }

            $path = $this->storeFile($file, $establecimiento, 'galeria');

            $documento = Documento::query()->create([
                'nombre_original' => $file->getClientOriginalName(),
                'nombre_guardado' => basename($path),
                'ruta_archivo' => $path,
                'id_tipo_documento' => $galleryType->id_tipo_documento,
            ]);

            $establecimiento->documentos()->syncWithoutDetaching([$documento->id_documento]);
        }
    }

    private function storeFile(UploadedFile $file, Establecimiento $establecimiento, string $folder): string
    {
        return ImageManager::storePublicDiskFile(
            $file,
            sprintf('commerce-registration/%s/%s', $establecimiento->id_establecimiento, $folder)
        );
    }

    private function resolveDocumentType(string $typeName): TipoDocumento
    {
        return TipoDocumento::query()->firstOrCreate(
            ['nombre' => $typeName],
            ['descripcion' => 'Tipo de documento creado desde el registro de comercios.']
        );
    }
}
