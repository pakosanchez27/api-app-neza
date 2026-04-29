<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TipoDocumento;
use Illuminate\Http\JsonResponse;

class TipoDocumentoController extends Controller
{
    public function index(): JsonResponse
    {
        $tiposDocumento = TipoDocumento::query()
            ->orderBy('id_tipo_documento')
            ->get();

        return response()->json($tiposDocumento);
    }
}
