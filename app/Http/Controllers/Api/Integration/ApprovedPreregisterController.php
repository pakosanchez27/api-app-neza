<?php

namespace App\Http\Controllers\Api\Integration;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApprovedPreregisterStoreRequest;
use App\Models\User;
use App\Services\ApprovedPreregisterIntegrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ApprovedPreregisterController extends Controller
{
    public function __construct(private readonly ApprovedPreregisterIntegrationService $integrationService)
    {
    }

    public function store(ApprovedPreregisterStoreRequest $request): JsonResponse
    {
        $configuredKey = (string) env('INTEGRATION_API_KEY', '');
        $incomingKey = (string) $request->header('X-Integration-Key', '');

        if ($configuredKey === '') {
            return response()->json([
                'message' => 'La llave de integración no está configurada.',
            ], 500);
        }

        if (! hash_equals($configuredKey, $incomingKey)) {
            return response()->json([
                'message' => 'No autorizado para usar este endpoint.',
            ], 401);
        }

        $data = $request->validated();
        $email = $data['solicitante']['email'];

        if (User::query()->where('email', $email)->exists()) {
            return response()->json([
                'message' => 'Ya existe un usuario con ese correo.',
                'email' => $email,
            ], 409);
        }

        try {
            $this->integrationService->ensureAdminComerciosRole();
        } catch (\RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 500);
        }

        $temporaryPassword = Str::slug(Str::random(8));

        $result = $this->integrationService->createApprovedCommerce(
            $data,
            (bool) ($data['establecimiento']['is_route'] ?? false),
            $temporaryPassword
        );

        return response()->json([
            'message' => 'Prerregistro aprobado e integrado correctamente.',
            'folio_preregistro' => $data['folio_preregistro'] ?? null,
            'temporary_password' => $result['temporary_password'],
            'user' => [
                'id' => $result['user']->id,
                'email' => $result['user']->email,
                'id_rol' => $result['user']->id_rol,
            ],
            'establecimiento' => [
                'id_establecimiento' => $result['establecimiento']->id_establecimiento,
                'nombre_est' => $result['establecimiento']->nombre_est,
            ],
        ], 201);
    }
}
