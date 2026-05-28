<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PuntoMapa;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PuntoMapaController extends Controller
{
    public function index(): JsonResponse
    {
        $puntosMapa = PuntoMapa::query()
            ->with('categoria:id,tipo')
            ->where('estatus', 1)
            ->orderBy('categoria_id')
            ->orderBy('nombre_punto')
            ->get()
            ->map(function (PuntoMapa $puntoMapa): array {
                $addressParts = array_filter([
                    $puntoMapa->calle,
                    $puntoMapa->numero_exterior,
                    $puntoMapa->numero_interior ? 'Int. ' . $puntoMapa->numero_interior : null,
                    $puntoMapa->colonia,
                    $puntoMapa->cp,
                ], fn ($value) => filled($value));

                return [
                    'id' => $puntoMapa->id,
                    'name' => $puntoMapa->nombre_punto,
                    'category' => $puntoMapa->categoria?->tipo ?? 'Sin categoria',
                    'description' => $puntoMapa->descripcion,
                    'image' => $puntoMapa->foto_principal
                        ? route('api.puntos-mapa.foto', $puntoMapa)
                        : null,
                    'address' => implode(', ', $addressParts),
                    'phone' => $puntoMapa->telefono,
                    'email' => $puntoMapa->email,
                    'hours' => $puntoMapa->horarios,
                    'position' => [
                        (float) $puntoMapa->latitud,
                        (float) $puntoMapa->longitud,
                    ],
                    'latitud' => (float) $puntoMapa->latitud,
                    'longitud' => (float) $puntoMapa->longitud,
                    'calle' => $puntoMapa->calle,
                    'numero_exterior' => $puntoMapa->numero_exterior,
                    'numero_interior' => $puntoMapa->numero_interior,
                    'cp' => $puntoMapa->cp,
                    'colonia' => $puntoMapa->colonia,
                ];
            })
            ->values();

        return response()->json($puntosMapa);
    }

    public function photo(PuntoMapa $puntoMapa): BinaryFileResponse
    {
        abort_if(! $puntoMapa->foto_principal, 404);

        $absolutePath = storage_path('app/' . ltrim(str_replace('\\', '/', $puntoMapa->foto_principal), '/'));

        abort_unless(File::exists($absolutePath), 404);

        return response()->file($absolutePath);
    }
}
