<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Amenidad;
use Illuminate\Http\JsonResponse;

class AmenidadController extends Controller
{
    public function index(): JsonResponse
    {
        $amenidades = Amenidad::query()
            ->orderBy('nombre')
            ->get();

        return response()->json($amenidades);
    }
}
