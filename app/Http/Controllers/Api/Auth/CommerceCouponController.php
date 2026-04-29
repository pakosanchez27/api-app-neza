<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCuponRequest;
use App\Models\Cupon;
use App\Models\RedencionCupon;
use App\Models\User;
use App\Models\UsuarioCupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommerceCouponController extends Controller
{
    public function index(): JsonResponse
    {
        /** @var User $user */
        $user = request()->user();
        $establecimiento = $user->establecimientos()->first();

        if (!$establecimiento) {
            return response()->json([
                'message' => 'No se encontro un establecimiento asociado a esta cuenta.',
            ], 404);
        }

        $cupones = Cupon::query()
            ->with('establecimiento.tipo')
            ->withCount('usuariosCupones')
            ->withCount([
                'usuariosCupones as redimidos_count' => function ($query) {
                    $query->where('status', 'redeemed');
                },
            ])
            ->where('id_establecimiento', $establecimiento->id_establecimiento)
            ->latest('id')
            ->get();
        return response()->json($cupones);
    }

    public function store(StoreCuponRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $establecimiento = $user->establecimientos()->first();

        if (!$establecimiento) {
            return response()->json([
                'message' => 'No se encontro un establecimiento asociado a esta cuenta.',
            ], 404);
        }

        $validated = $request->validated();

        $cupon = Cupon::query()->create([
            'id_establecimiento' => $establecimiento->id_establecimiento,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'stock' => $validated['stock'],
            'claim_limit_per_user' => $validated['claim_limit_per_user'] ?? 1,
            'starts_at' => $validated['starts_at'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'terms' => $validated['terms'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $cupon->load('establecimiento.tipo');

        return response()->json([
            'message' => 'Cupon creado correctamente.',
            'cupon' => $cupon,
        ], 201);
    }

    public function update(StoreCuponRequest $request, int $cuponId): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $establecimiento = $user->establecimientos()->first();

        if (!$establecimiento) {
            return response()->json([
                'message' => 'No se encontro un establecimiento asociado a esta cuenta.',
            ], 404);
        }

        $cupon = Cupon::query()
            ->where('id_establecimiento', $establecimiento->id_establecimiento)
            ->find($cuponId);

        if (!$cupon) {
            return response()->json([
                'message' => 'Cupon no encontrado para este establecimiento.',
            ], 404);
        }

        $validated = $request->validated();

        $cupon->forceFill([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'stock' => $validated['stock'],
            'claim_limit_per_user' => $validated['claim_limit_per_user'] ?? 1,
            'starts_at' => $validated['starts_at'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'terms' => $validated['terms'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ])->save();

        $cupon->refresh()->load('establecimiento.tipo');

        return response()->json([
            'message' => 'Cupon actualizado correctamente.',
            'cupon' => $cupon,
        ]);
    }

    public function redeemByCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'unique_code' => ['required', 'string'],
        ]);

        /** @var User $user */
        $user = $request->user();
        $establecimiento = $user->establecimientos()->first();

        if (!$establecimiento) {
            return response()->json([
                'message' => 'No se encontro un establecimiento asociado a esta cuenta.',
            ], 404);
        }

        $usuarioCupon = UsuarioCupon::query()
            ->with(['cupon.establecimiento', 'usuario'])
            ->where('unique_code', trim($validated['unique_code']))
            ->first();

        if (!$usuarioCupon || !$usuarioCupon->cupon) {
            return response()->json([
                'message' => 'No se encontro un cupon con ese codigo.',
            ], 404);
        }

        if ((int) $usuarioCupon->cupon->id_establecimiento !== (int) $establecimiento->id_establecimiento) {
            return response()->json([
                'message' => 'Este codigo no pertenece a tu establecimiento.',
            ], 403);
        }

        if ($usuarioCupon->status === 'redeemed') {
            return response()->json([
                'message' => 'Este cupon ya fue usado anteriormente.',
                'usuario_cupon' => $usuarioCupon,
            ], 422);
        }

        if ($usuarioCupon->status !== 'claimed') {
            return response()->json([
                'message' => 'Este cupon no se puede redimir en su estado actual.',
            ], 422);
        }

        if ($usuarioCupon->cupon->expires_at && $usuarioCupon->cupon->expires_at->isPast()) {
            $usuarioCupon->forceFill([
                'status' => 'expired',
                'expired_at' => now(),
            ])->save();

            return response()->json([
                'message' => 'Este cupon ya vencio y no puede usarse.',
            ], 422);
        }

        DB::transaction(function () use ($usuarioCupon, $establecimiento, $user) {
            $usuarioCupon->forceFill([
                'status' => 'redeemed',
                'redeemed_at' => now(),
            ])->save();

            RedencionCupon::query()->create([
                'user_coupon_id' => $usuarioCupon->id,
                'id_establecimiento' => $establecimiento->id_establecimiento,
                'redeemed_by_user_id' => $user->id,
                'redeemed_at' => now(),
            ]);
        });

        $usuarioCupon->refresh()->load(['cupon.establecimiento', 'usuario']);

        return response()->json([
            'message' => 'Cupon usado correctamente.',
            'usuario_cupon' => $usuarioCupon,
        ]);
    }

    public function redemptions(int $cuponId): JsonResponse
    {
        /** @var User $user */
        $user = request()->user();
        $establecimiento = $user->establecimientos()->first();

        if (!$establecimiento) {
            return response()->json([
                'message' => 'No se encontro un establecimiento asociado a esta cuenta.',
            ], 404);
        }

        $cupon = Cupon::query()
            ->with('establecimiento.tipo')
            ->where('id_establecimiento', $establecimiento->id_establecimiento)
            ->find($cuponId);

        if (!$cupon) {
            return response()->json([
                'message' => 'Cupon no encontrado para este establecimiento.',
            ], 404);
        }

        $redemptions = UsuarioCupon::query()
            ->with(['usuario', 'redenciones' => function ($query) {
                $query->latest('redeemed_at')->latest('id');
            }])
            ->where('coupon_id', $cupon->id)
            ->where('status', 'redeemed')
            ->latest('redeemed_at')
            ->latest('id')
            ->get()
            ->map(function (UsuarioCupon $usuarioCupon) {
                $usuario = $usuarioCupon->usuario;
                $latestRedemption = $usuarioCupon->redenciones->first();

                return [
                    'id' => $usuarioCupon->id,
                    'unique_code' => $usuarioCupon->unique_code,
                    'redeemed_at' => optional($usuarioCupon->redeemed_at)?->toIso8601String(),
                    'claimed_at' => optional($usuarioCupon->claimed_at)?->toIso8601String(),
                    'user' => [
                        'id' => $usuario?->id,
                        'name' => $usuario?->nombre_p ?: $usuario?->name,
                        'email' => $usuario?->email,
                        'telefono' => $usuario?->telefono,
                    ],
                    'redemption' => [
                        'redeemed_at' => optional($latestRedemption?->redeemed_at)?->toIso8601String(),
                        'notes' => $latestRedemption?->notes,
                    ],
                ];
            })
            ->values();

        return response()->json([
            'cupon' => [
                'id' => $cupon->id,
                'title' => $cupon->title,
                'description' => $cupon->description,
            ],
            'redenciones' => $redemptions,
        ]);
    }
}
