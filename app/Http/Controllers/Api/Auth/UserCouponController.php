<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cupon;
use App\Models\User;
use App\Models\UsuarioCupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserCouponController extends Controller
{
    public function index(): JsonResponse
    {
        /** @var User $user */
        $user = request()->user();

        $cupones = UsuarioCupon::query()
            ->with('cupon.establecimiento.tipo')
            ->where('user_id', $user->id)
            ->latest('claimed_at')
            ->latest('id')
            ->get()
            ->map(fn (UsuarioCupon $usuarioCupon) => $this->transformUsuarioCupon($usuarioCupon))
            ->values();

        return response()->json($cupones);
    }

    public function store(int $cuponId): JsonResponse
    {
        /** @var User $user */
        $user = request()->user();

        $cupon = Cupon::query()
            ->with('establecimiento')
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
            ->find($cuponId);

        if (!$cupon) {
            return response()->json([
                'message' => 'Cupon no disponible.',
            ], 404);
        }

        $alreadyClaimedCount = UsuarioCupon::query()
            ->where('user_id', $user->id)
            ->where('coupon_id', $cupon->id)
            ->whereIn('status', ['claimed', 'redeemed'])
            ->count();

        if ($alreadyClaimedCount >= (int) $cupon->claim_limit_per_user) {
            return response()->json([
                'message' => 'Ya alcanzaste el limite de guardados para este cupon.',
            ], 422);
        }

        $claimedCount = UsuarioCupon::query()
            ->where('coupon_id', $cupon->id)
            ->whereIn('status', ['claimed', 'redeemed'])
            ->count();

        if ($claimedCount >= (int) $cupon->stock) {
            return response()->json([
                'message' => 'Este cupon ya no tiene disponibilidad.',
            ], 422);
        }

        $usuarioCupon = DB::transaction(function () use ($user, $cupon) {
            return UsuarioCupon::query()->create([
                'user_id' => $user->id,
                'coupon_id' => $cupon->id,
                'status' => 'claimed',
                'unique_code' => $this->generateUniqueCode(),
                'claimed_at' => now(),
            ]);
        });

        return response()->json([
            'message' => 'Cupon guardado correctamente.',
            'usuario_cupon' => $usuarioCupon,
        ], 201);
    }

    private function generateUniqueCode(): string
    {
        do {
            $code = Str::upper(Str::random(24));
        } while (UsuarioCupon::query()->where('unique_code', $code)->exists());

        return $code;
    }

    private function transformUsuarioCupon(UsuarioCupon $usuarioCupon): array
    {
        $cupon = $usuarioCupon->cupon;
        $establecimiento = $cupon?->establecimiento;
        $logoUrl = null;

        if ($establecimiento?->logo) {
            $logoUrl = preg_match('/^https?:\/\//i', $establecimiento->logo)
                ? $establecimiento->logo
                : Storage::disk('public')->url($establecimiento->logo);
        }

        return [
            'id' => $usuarioCupon->id,
            'status' => $usuarioCupon->status,
            'unique_code' => $usuarioCupon->unique_code,
            'claimed_at' => optional($usuarioCupon->claimed_at)?->toIso8601String(),
            'redeemed_at' => optional($usuarioCupon->redeemed_at)?->toIso8601String(),
            'expired_at' => optional($usuarioCupon->expired_at)?->toIso8601String(),
            'cupon' => [
                'id' => $cupon?->id,
                'title' => $cupon?->title,
                'description' => $cupon?->description,
                'discount_type' => $cupon?->discount_type,
                'discount_value' => $cupon ? (float) $cupon->discount_value : null,
                'stock' => $cupon?->stock,
                'starts_at' => optional($cupon?->starts_at)?->toIso8601String(),
                'expires_at' => optional($cupon?->expires_at)?->toIso8601String(),
                'terms' => $cupon?->terms,
                'establecimiento' => [
                    'id' => $establecimiento?->id_establecimiento,
                    'nombre' => $establecimiento?->nombre_est,
                    'tipo' => $establecimiento?->tipo?->nombre,
                    'logo_url' => $logoUrl,
                ],
            ],
        ];
    }
}
