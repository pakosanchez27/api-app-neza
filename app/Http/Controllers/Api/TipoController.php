<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tipo;
use Illuminate\Http\JsonResponse;

class TipoController extends Controller
{
    public function index(): JsonResponse
    {
        $tipos = Tipo::query()
            ->orderBy('id_tipo')
            ->get();

        return response()->json($tipos);
    }
}
