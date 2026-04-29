<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Establecimiento;
use Illuminate\Http\JsonResponse;

class EstablecimientoController extends Controller
{
    public function index(): JsonResponse
    {
        $establecimientos = Establecimiento::query()
            ->with([
                'tipo',
                'contacto',
                'domicilio',
                'horarios',
                'amenidades',
                'documentos.tipoDocumento',
            ])
            ->orderBy('id_establecimiento')
            ->get();

        return response()->json($establecimientos);
    }

    public function show(int $id): JsonResponse
    {
        $establecimiento = Establecimiento::query()
            ->with([
                'tipo',
                'contacto',
                'domicilio',
                'horarios',
                'amenidades',
                'documentos.tipoDocumento',
                'user.role',
            ])
            ->find($id);

        if (!$establecimiento) {
            return response()->json([
                'message' => 'Establecimiento no encontrado.',
            ], 404);
        }

        return response()->json($establecimiento);
    }
}
