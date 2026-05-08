<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Establecimiento;
use Illuminate\Http\JsonResponse;

class EstablecimientoController extends Controller
{
    private function baseQuery()
    {
        return Establecimiento::query()
            ->with([
                'tipo',
                'contacto',
                'domicilio',
                'horarios',
                'amenidades',
                'documentos.tipoDocumento',
            ]);
    }

    public function index(): JsonResponse
    {
        $establecimientos = $this->baseQuery()
            ->orderBy('id_establecimiento')
            ->get();

        return response()->json($establecimientos);
    }

    public function route(): JsonResponse
    {
        $establecimientos = $this->baseQuery()
            ->where('is_route', true)
            ->orderBy('id_establecimiento')
            ->get();

        return response()->json($establecimientos);
    }

    public function show(int $id): JsonResponse
    {
        $establecimiento = $this->baseQuery()
            ->with('user.role')
            ->find($id);

        if (!$establecimiento) {
            return response()->json([
                'message' => 'Establecimiento no encontrado.',
            ], 404);
        }

        return response()->json($establecimiento);
    }
}
