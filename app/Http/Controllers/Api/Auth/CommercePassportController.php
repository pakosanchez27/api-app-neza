<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasaporteSello;
use App\Models\Ruta;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommercePassportController extends Controller
{
    private const QR_WINDOW_SECONDS = 10;
    private const QR_VERSION = 'v1';

    public function qr(Request $request): JsonResponse
    {
        $establecimiento = $this->resolveCommerceEstablishment($request);

        if (!$establecimiento) {
            return response()->json([
                'message' => 'No se encontro un establecimiento asociado a la cuenta.',
            ], 404);
        }

        if (!$establecimiento->is_route) {
            return response()->json([
                'message' => 'Este establecimiento no esta habilitado para la ruta del pasaporte.',
            ], 422);
        }

        /** @var Ruta|null $ruta */
        $ruta = Ruta::query()
            ->where('is_active', true)
            ->orderBy('id_ruta')
            ->first();

        if (!$ruta) {
            return response()->json([
                'message' => 'No se encontro una ruta activa para generar el QR.',
            ], 422);
        }

        $issuedAt = now()->timestamp;
        $expiresAt = $issuedAt + self::QR_WINDOW_SECONDS;
        $token = $this->buildDynamicQrToken(
            (int) $establecimiento->id_establecimiento,
            (int) $ruta->id_ruta,
            $issuedAt,
            $expiresAt
        );

        return response()->json([
            'route' => [
                'id_ruta' => $ruta->id_ruta,
                'nombre' => $ruta->nombre,
                'slug' => $ruta->slug,
            ],
            'establishment' => [
                'id_establecimiento' => $establecimiento->id_establecimiento,
                'nombre_est' => $establecimiento->nombre_est,
                'logo' => $establecimiento->logo,
            ],
            'qr' => [
                'token' => $token,
                'issued_at' => now()->toIso8601String(),
                'expires_at' => now()->addSeconds(self::QR_WINDOW_SECONDS)->toIso8601String(),
                'expires_in_seconds' => self::QR_WINDOW_SECONDS,
            ],
        ]);
    }

    public function stamps(Request $request): JsonResponse
    {
        $establecimiento = $this->resolveCommerceEstablishment($request);

        if (!$establecimiento) {
            return response()->json([
                'message' => 'No se encontro un establecimiento asociado a la cuenta.',
            ], 404);
        }

        $stamps = PasaporteSello::query()
            ->with(['pasaporte.user', 'pasaporte.ruta'])
            ->where('id_establecimiento', $establecimiento->id_establecimiento)
            ->latest('sealed_at')
            ->latest('id_pasaporte_sello')
            ->get();

        $activity = $stamps
            ->map(function (PasaporteSello $seal) {
                $passport = $seal->pasaporte;
                $user = $passport?->user;
                $route = $passport?->ruta;

                return [
                    'id' => $seal->id_pasaporte_sello,
                    'stamped_at' => $seal->sealed_at?->toIso8601String(),
                    'passport_code' => $seal->qr_token_usado,
                    'completed' => $passport?->completed_at !== null,
                    'completed_at' => $passport?->completed_at?->toIso8601String(),
                    'user' => [
                        'id' => $user?->id,
                        'name' => $user?->nombre_p ?: $user?->name,
                        'email' => $user?->email,
                        'telefono' => $user?->telefono,
                    ],
                    'route' => [
                        'id_ruta' => $route?->id_ruta,
                        'nombre' => $route?->nombre,
                        'slug' => $route?->slug,
                    ],
                ];
            })
            ->values();

        $uniqueVisitors = $stamps
            ->pluck('pasaporte.user_id')
            ->filter()
            ->unique()
            ->count();

        $completedPassports = $stamps
            ->filter(fn (PasaporteSello $seal) => $seal->pasaporte?->completed_at !== null)
            ->pluck('pasaporte.id_pasaporte')
            ->filter()
            ->unique()
            ->count();

        return response()->json([
            'stats' => [
                'total_stamps' => $stamps->count(),
                'unique_visitors' => $uniqueVisitors,
                'completed_passports' => $completedPassports,
            ],
            'sellos' => $activity,
        ]);
    }

    public static function parseDynamicQrToken(string $token): ?array
    {
        $parts = explode('.', trim($token));

        if (count($parts) !== 3 || $parts[0] !== 'NEZA-PASS-' . self::QR_VERSION) {
            return null;
        }

        $payloadJson = self::base64UrlDecode($parts[1]);

        if ($payloadJson === false) {
            return null;
        }

        $payload = json_decode($payloadJson, true);

        if (!is_array($payload)) {
            return null;
        }

        $signature = self::signPayload($parts[1]);

        if (!hash_equals($signature, $parts[2])) {
            return null;
        }

        return $payload;
    }

    public static function isDynamicQrTokenValidForWindow(array $payload): bool
    {
        $now = now()->timestamp;
        $issuedAt = (int) ($payload['iat'] ?? 0);
        $expiresAt = (int) ($payload['exp'] ?? 0);

        if ($issuedAt <= 0 || $expiresAt <= 0 || $expiresAt <= $issuedAt) {
            return false;
        }

        return $now >= $issuedAt && $now <= ($expiresAt + self::QR_WINDOW_SECONDS);
    }

    private function buildDynamicQrToken(
        int $establishmentId,
        int $routeId,
        int $issuedAt,
        int $expiresAt
    ): string {
        $payload = [
            'est' => $establishmentId,
            'route' => $routeId,
            'iat' => $issuedAt,
            'exp' => $expiresAt,
        ];

        $payloadEncoded = self::base64UrlEncode(json_encode($payload, JSON_UNESCAPED_SLASHES));
        $signature = self::signPayload($payloadEncoded);

        return 'NEZA-PASS-' . self::QR_VERSION . '.' . $payloadEncoded . '.' . $signature;
    }

    private static function signPayload(string $payloadEncoded): string
    {
        $appKey = (string) config('app.key');
        $normalizedKey = str_starts_with($appKey, 'base64:')
            ? base64_decode(substr($appKey, 7), true)
            : $appKey;

        if ($normalizedKey === false || $normalizedKey === '') {
            $normalizedKey = $appKey;
        }

        return self::base64UrlEncode(hash_hmac('sha256', $payloadEncoded, $normalizedKey, true));
    }

    private static function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $value): string|false
    {
        $padding = strlen($value) % 4;

        if ($padding > 0) {
            $value .= str_repeat('=', 4 - $padding);
        }

        return base64_decode(strtr($value, '-_', '+/'), true);
    }

    private function resolveCommerceEstablishment(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        return $user->establecimientos()->first();
    }
}
