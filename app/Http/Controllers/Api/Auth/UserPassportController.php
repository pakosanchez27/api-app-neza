<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Establecimiento;
use App\Models\PasaporteSello;
use App\Models\PasaporteUsuario;
use App\Models\Ruta;
use App\Models\User;
use App\Support\ImageManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserPassportController extends Controller
{
    public function show(Request $request, Ruta $ruta): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $pasaporte = $this->findOrCreatePassport($user, $ruta);

        return response()->json($this->buildPassportPayload($pasaporte));
    }

    public function seal(Request $request, Ruta $ruta): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'qr_token' => ['required', 'string', 'max:255'],
        ]);

        $pasaporte = DB::transaction(function () use ($user, $ruta, $validated) {
            $pasaporte = $this->findOrCreatePassport($user, $ruta);
            $qrToken = trim($validated['qr_token']);
            $establecimiento = $this->resolveEstablishmentFromQrToken($ruta, $qrToken);

            if (!$establecimiento instanceof Establecimiento) {
                throw new HttpResponseException(response()->json([
                    'message' => 'El QR no corresponde a un establecimiento activo de esta ruta.',
                ], 422));
            }

            $duplicateSeal = PasaporteSello::query()
                ->where('id_pasaporte', $pasaporte->id_pasaporte)
                ->where('id_establecimiento', $establecimiento->id_establecimiento)
                ->exists();

            if ($duplicateSeal) {
                throw new HttpResponseException(response()->json([
                    'message' => 'Este establecimiento ya fue sellado en tu pasaporte.',
                ], 409));
            }

            PasaporteSello::query()->create([
                'id_pasaporte' => $pasaporte->id_pasaporte,
                'id_establecimiento' => $establecimiento->id_establecimiento,
                'qr_token_usado' => $qrToken,
                'sealed_at' => now(),
            ]);

            return $this->refreshCompletionStatus($pasaporte);
        });

        return response()->json([
            'message' => 'Sello registrado correctamente.',
            'passport' => $this->buildPassportPayload($pasaporte),
        ]);
    }

    private function findOrCreatePassport(User $user, Ruta $ruta): PasaporteUsuario
    {
        return PasaporteUsuario::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'id_ruta' => $ruta->id_ruta,
            ],
            [
                'completed_at' => null,
            ]
        );
    }

    private function refreshCompletionStatus(PasaporteUsuario $pasaporte): PasaporteUsuario
    {
        $pasaporte->refresh()->load(['ruta', 'sellos']);

        $visitedCount = $pasaporte->sellos->count();
        $totalCount = $this->eligibleEstablishmentsQuery($pasaporte->ruta)->count();

        $pasaporte->forceFill([
            'completed_at' => $totalCount > 0 && $visitedCount >= $totalCount ? now() : null,
        ])->save();

        return $pasaporte->refresh()->load(['ruta', 'sellos']);
    }

    private function buildPassportPayload(PasaporteUsuario $pasaporte): array
    {
        $pasaporte->loadMissing(['ruta', 'sellos']);

        $routeEstablishments = $this->eligibleEstablishmentsQuery($pasaporte->ruta)
            ->orderBy('id_establecimiento')
            ->get([
                'establecimientos.id_establecimiento',
                'establecimientos.nombre_est',
                'establecimientos.logo',
                'establecimientos.descripcion',
            ]);

        $sealMap = PasaporteSello::query()
            ->where('id_pasaporte', $pasaporte->id_pasaporte)
            ->get()
            ->keyBy('id_establecimiento');

        $visitedCount = $sealMap->count();
        $totalCount = $routeEstablishments->count();
        $progressPercentage = $totalCount > 0
            ? round(($visitedCount / $totalCount) * 100, 2)
            : 0;

        $stamps = $routeEstablishments
            ->map(function (Establecimiento $establecimiento) use ($sealMap) {
                /** @var PasaporteSello|null $seal */
                $seal = $sealMap->get($establecimiento->id_establecimiento);

                return [
                    'id_establecimiento' => $establecimiento->id_establecimiento,
                    'name' => $establecimiento->nombre_est,
                    'logo_url' => $this->resolvePublicFileUrl($establecimiento->logo),
                    'status' => $seal ? 'visitado' : 'bloqueado',
                    'detail' => $seal && $seal->sealed_at
                        ? 'Visitado ' . $seal->sealed_at->format('d/m/Y')
                        : 'Disponible para visitar',
                    'visited_at' => $seal?->sealed_at?->toIso8601String(),
                ];
            })
            ->values();

        return [
            'route' => [
                'id_ruta' => $pasaporte->ruta->id_ruta,
                'nombre' => $pasaporte->ruta->nombre,
                'slug' => $pasaporte->ruta->slug,
                'descripcion' => $pasaporte->ruta->descripcion,
            ],
            'passport' => [
                'id_pasaporte' => $pasaporte->id_pasaporte,
                'visited_count' => $visitedCount,
                'total_count' => $totalCount,
                'progress_percentage' => $progressPercentage,
                'completed' => $pasaporte->completed_at !== null,
                'completed_at' => $pasaporte->completed_at?->toIso8601String(),
            ],
            'stamps' => $stamps,
        ];
    }

    private function eligibleEstablishmentsQuery(Ruta $ruta)
    {
        return Establecimiento::query()
            ->where('is_route', true)
            ->where('estatus', true)
            ->where('is_visible', true);
    }

    private function resolveEstablishmentFromQrToken(Ruta $ruta, string $qrToken): ?Establecimiento
    {
        $dynamicPayload = CommercePassportController::parseDynamicQrToken($qrToken);

        if (is_array($dynamicPayload)) {
            $payloadRouteId = (int) ($dynamicPayload['route'] ?? 0);
            $payloadEstablishmentId = (int) ($dynamicPayload['est'] ?? 0);

            if (
                $payloadRouteId !== (int) $ruta->id_ruta ||
                $payloadEstablishmentId <= 0 ||
                !CommercePassportController::isDynamicQrTokenValidForWindow($dynamicPayload)
            ) {
                return null;
            }

            return Establecimiento::query()
                ->where('id_establecimiento', $payloadEstablishmentId)
                ->where('is_route', true)
                ->where('estatus', true)
                ->where('is_visible', true)
                ->first();
        }

        return Establecimiento::query()
            ->where('qr_token', $qrToken)
            ->where('is_route', true)
            ->where('estatus', true)
            ->where('is_visible', true)
            ->first();
    }

    private function resolvePublicFileUrl(?string $path): string
    {
        if (!$path) {
            return '';
        }

        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }

        return ImageManager::storageUrl($path);
    }
}
