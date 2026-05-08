<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
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
        /** @var User $user */
        $user = $request->user();
        $establecimiento = $user->establecimientos()->with('rutas')->first();

        if (!$establecimiento) {
            return response()->json([
                'message' => 'No se encontro un establecimiento asociado a la cuenta.',
            ], 404);
        }

        /** @var Ruta|null $ruta */
        $ruta = $establecimiento->rutas()
            ->where('rutas.is_active', true)
            ->orderBy('rutas.id_ruta')
            ->first();

        if (!$ruta) {
            return response()->json([
                'message' => 'Este establecimiento no pertenece a una ruta activa.',
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
}
