<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ruta;
use Illuminate\Http\JsonResponse;

class RutaController extends Controller
{
    public function index(): JsonResponse
    {
        $rutas = Ruta::query()
            ->where('is_active', true)
            ->orderBy('id_ruta')
            ->get()
            ->map(function (Ruta $ruta) {
                return [
                    'id_ruta' => $ruta->id_ruta,
                    'nombre' => $ruta->nombre,
                    'slug' => $ruta->slug,
                    'descripcion' => $ruta->descripcion,
                    'is_active' => $ruta->is_active,
                ];
            })
            ->values();

        return response()->json($rutas);
    }

    public function establecimientos(Ruta $ruta): JsonResponse
    {
        $establecimientos = $ruta->establecimientos()
            ->with([
                'tipo',
                'contacto',
                'domicilio',
                'horarios',
                'amenidades',
                'documentos.tipoDocumento',
            ])
            ->where('establecimientos.estatus', true)
            ->where('establecimientos.is_visible', true)
            ->orderByPivot('orden')
            ->get();

        return response()->json([
            'ruta' => [
                'id_ruta' => $ruta->id_ruta,
                'nombre' => $ruta->nombre,
                'slug' => $ruta->slug,
                'descripcion' => $ruta->descripcion,
            ],
            'establecimientos' => $establecimientos,
        ]);
    }
}
