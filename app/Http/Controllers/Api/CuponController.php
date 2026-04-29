<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class CuponController extends Controller
{
    public function index(): JsonResponse
    {
        $cupones = Cupon::query()
            ->with('establecimiento.tipo')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })
            ->whereHas('establecimiento', function ($query) {
                $query->where('estatus', true)
                    ->where('is_visible', true);
            })
            ->latest('id')
            ->get()
            ->map(fn (Cupon $cupon) => $this->transformCupon($cupon))
            ->values();

        return response()->json($cupones);
    }

    private function transformCupon(Cupon $cupon): array
    {
        $establecimiento = $cupon->establecimiento;
        $logoUrl = null;

        if ($establecimiento?->logo) {
            $logoUrl = preg_match('/^https?:\/\//i', $establecimiento->logo)
                ? $establecimiento->logo
                : Storage::disk('public')->url($establecimiento->logo);
        }

        return [
            'id' => $cupon->id,
            'title' => $cupon->title,
            'description' => $cupon->description,
            'discount_type' => $cupon->discount_type,
            'discount_value' => (float) $cupon->discount_value,
            'stock' => (int) $cupon->stock,
            'claim_limit_per_user' => (int) $cupon->claim_limit_per_user,
            'starts_at' => optional($cupon->starts_at)?->toIso8601String(),
            'expires_at' => optional($cupon->expires_at)?->toIso8601String(),
            'terms' => $cupon->terms,
            'is_active' => (bool) $cupon->is_active,
            'establecimiento' => [
                'id' => $establecimiento?->id_establecimiento,
                'nombre' => $establecimiento?->nombre_est,
                'descripcion' => $establecimiento?->descripcion,
                'logo' => $establecimiento?->logo,
                'logo_url' => $logoUrl,
                'tipo' => $establecimiento?->tipo?->nombre,
            ],
        ];
    }
}
