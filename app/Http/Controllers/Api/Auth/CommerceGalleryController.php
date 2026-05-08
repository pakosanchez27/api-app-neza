<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Documento;
use App\Models\Establecimiento;
use App\Models\TipoDocumento;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CommerceGalleryController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $establecimiento = $this->resolveEstablishment($user);

        $validator = Validator::make(array_merge($request->all(), $request->allFiles()), [
            'galeria' => ['required', 'array', 'min:1', 'max:5'],
            'galeria.*' => ['required', 'file', 'image', 'max:10240'],
        ], [
            'galeria.*.uploaded' => 'Una de las imagenes no se pudo cargar. Revisa el tamano del archivo o la configuracion del servidor.',
            'galeria.*.image' => 'Cada archivo debe ser una imagen valida.',
            'galeria.*.max' => 'Cada imagen no debe pesar mas de 10 MB.',
        ]);

        $validator->after(function ($validator) use ($establecimiento, $request) {
            $currentCount = $this->galleryQuery($establecimiento)->count();
            $newCount = count($request->file('galeria', []));

            if (($currentCount + $newCount) > 5) {
                $validator->errors()->add('galeria', 'Solo puedes tener hasta 5 fotos en la galeria.');
            }
        });

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        DB::transaction(function () use ($establecimiento, $request) {
            $galleryType = $this->resolveDocumentType('galeria');

            foreach ($request->file('galeria', []) as $file) {
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
        });

        return $this->profileResponse($user, 'Fotos agregadas correctamente.');
    }

    public function replace(Request $request, int $documentoId): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $establecimiento = $this->resolveEstablishment($user);
        $documento = $this->resolveGalleryDocument($establecimiento, $documentoId);

        $validator = Validator::make(array_merge($request->all(), $request->allFiles()), [
            'foto' => ['required', 'file', 'image', 'max:10240'],
        ], [
            'foto.uploaded' => 'La foto no se pudo cargar. Revisa el tamano del archivo o la configuracion del servidor.',
            'foto.image' => 'La foto debe ser una imagen valida.',
            'foto.max' => 'La foto no debe pesar mas de 10 MB.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        /** @var UploadedFile $file */
        $file = $request->file('foto');

        $path = $this->storeFile($file, $establecimiento, 'galeria');

        $documento->forceFill([
            'nombre_original' => $file->getClientOriginalName(),
            'nombre_guardado' => basename($path),
            'ruta_archivo' => $path,
        ])->save();

        return $this->profileResponse($user, 'Foto reemplazada correctamente.');
    }

    public function destroy(Request $request, int $documentoId): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $establecimiento = $this->resolveEstablishment($user);
        $documento = $this->resolveGalleryDocument($establecimiento, $documentoId);

        $establecimiento->documentos()->detach([$documento->id_documento]);
        $documento->delete();

        return $this->profileResponse($user, 'Foto eliminada correctamente.');
    }

    private function resolveEstablishment(User $user): Establecimiento
    {
        $establecimiento = $user->establecimientos()->first();

        abort_unless($establecimiento, 404, 'No se encontro un establecimiento asociado a esta cuenta.');

        return $establecimiento;
    }

    private function galleryQuery(Establecimiento $establecimiento)
    {
        $galleryType = $this->resolveDocumentType('galeria');

        return $establecimiento->documentos()
            ->where('id_tipo_documento', $galleryType->id_tipo_documento);
    }

    private function resolveGalleryDocument(Establecimiento $establecimiento, int $documentoId): Documento
    {
        $documento = $this->galleryQuery($establecimiento)
            ->where('documentos.id_documento', $documentoId)
            ->first();

        abort_unless($documento, 404, 'La foto solicitada no pertenece a este establecimiento.');

        return $documento;
    }

    private function resolveDocumentType(string $typeName): TipoDocumento
    {
        return TipoDocumento::query()->firstOrCreate(
            ['nombre' => $typeName],
            ['descripcion' => 'Tipo de documento creado desde la administracion de comercios.']
        );
    }

    private function storeFile(UploadedFile $file, Establecimiento $establecimiento, string $folder): string
    {
        return $file->store(
            sprintf('commerce-registration/%s/%s', $establecimiento->id_establecimiento, $folder),
            'public'
        );
    }

    private function profileResponse(User $user, string $message): JsonResponse
    {
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
            'message' => $message,
            'user' => $user,
        ]);
    }
}
