<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommerceAdminLoginRequest;
use App\Models\User;
use App\Support\ImageManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CommerceAdminAuthController extends Controller
{
    public function login(CommerceAdminLoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $user = User::query()
            ->with([
                'role',
                'establecimientos.tipo',
                'establecimientos.contacto',
                'establecimientos.domicilio',
                'establecimientos.horarios',
                'establecimientos.amenidades',
                'establecimientos.documentos.tipoDocumento',
            ])
            ->where('email', $credentials['email'])
            ->first();

        if (!$user || !Hash::check($credentials['password'], $user->password ?? '')) {
            return response()->json([
                'message' => 'Las credenciales proporcionadas no son validas.',
            ], 422);
        }

        if (!$user->activo) {
            return response()->json([
                'message' => 'La cuenta esta inactiva.',
            ], 403);
        }

        if (!$user->role || $user->role->nombre !== 'AdminComercios') {
            return response()->json([
                'message' => 'Este acceso es exclusivo para administradores de comercios.',
            ], 403);
        }

        $user->tokens()->delete();
        $token = $user->createToken('commerce-admin')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesion exitoso.',
            'token' => $token,
            'token_type' => 'Bearer',
            'must_change_password' => (bool) $user->is_password_templ,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'nombre_p' => $user->nombre_p,
                'app_p' => $user->app_p,
                'apm_p' => $user->apm_p,
                'email' => $user->email,
                'telefono' => $user->telefono,
                'foto_perfil' => ImageManager::preferStoragePath($user->foto_perfil),
                'role' => $user->role?->nombre,
                'establecimientos' => $user->establecimientos,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load([
            'role',
            'establecimientos.tipo',
            'establecimientos.contacto',
            'establecimientos.domicilio',
            'establecimientos.horarios',
            'establecimientos.amenidades',
            'establecimientos.documentos.tipoDocumento',
        ]);

        return response()->json($this->transformUserPayload($user));
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Sesion cerrada correctamente.',
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                'max:80',
                Rule::unique('users', 'email')->ignore($user->id, 'id'),
            ],
            'telefono' => ['required', 'string', 'min:10', 'max:20'],
        ]);

        $user->forceFill([
            'email' => $validated['email'],
            'telefono' => preg_replace('/\D+/', '', $validated['telefono']),
        ])->save();

        $user->refresh()->load([
            'role',
            'establecimientos.tipo',
            'establecimientos.contacto',
            'establecimientos.domicilio',
            'establecimientos.horarios',
            'establecimientos.amenidades',
            'establecimientos.documentos.tipoDocumento',
        ]);

        return response()->json([
            'message' => 'Datos de acceso actualizados correctamente.',
            'user' => $this->transformUserPayload($user),
        ]);
    }

    public function updateVisibility(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'is_visible' => ['required', 'boolean'],
        ]);

        $establecimiento = $user->establecimientos()->first();

        if (!$establecimiento) {
            return response()->json([
                'message' => 'No se encontro un establecimiento asociado a la cuenta.',
            ], 404);
        }

        $establecimiento->forceFill([
            'is_visible' => (bool) $validated['is_visible'],
        ])->save();

        $user->refresh()->load([
            'role',
            'establecimientos.tipo',
            'establecimientos.contacto',
            'establecimientos.domicilio',
            'establecimientos.horarios',
            'establecimientos.amenidades',
            'establecimientos.documentos.tipoDocumento',
        ]);

        return response()->json([
            'message' => 'Visibilidad actualizada correctamente.',
            'user' => $this->transformUserPayload($user),
        ]);
    }

    public function updateUserProfile(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:180'],
            'foto_perfil' => ['nullable', 'image', 'max:10240'],
        ], [
            'foto_perfil.uploaded' => 'No se pudo cargar la foto de perfil. Revisa el tamano del archivo o la configuracion del servidor.',
            'foto_perfil.image' => 'La foto de perfil debe ser una imagen valida.',
            'foto_perfil.max' => 'La foto de perfil no debe pesar mas de 10 MB.',
        ]);



        $fullName = trim($validated['full_name']);
        [$nombre, $apellidoPaterno, $apellidoMaterno] = $this->splitFullName($fullName);

        $updates = [
            'name' => $fullName,
            'nombre_p' => $nombre,
            'app_p' => $apellidoPaterno,
            'apm_p' => $apellidoMaterno,
        ];

        if ($request->hasFile('foto_perfil')) {
            if ($user->foto_perfil) {
                Storage::disk('public')->delete($user->foto_perfil);
            }

            $updates['foto_perfil'] = ImageManager::storePublicDiskFile(
                $request->file('foto_perfil'),
                "commerce-profile/{$user->id}"
            );
        }

        $user->forceFill($updates)->save();

        $user->refresh()->load([
            'role',
            'establecimientos.tipo',
            'establecimientos.contacto',
            'establecimientos.domicilio',
            'establecimientos.horarios',
            'establecimientos.amenidades',
            'establecimientos.documentos.tipoDocumento',
        ]);

        return response()->json([
            'message' => 'Perfil actualizado correctamente.',
            'user' => $this->transformUserPayload($user),
        ]);
    }

    private function transformUserPayload(User $user): array
    {
        $payload = $user->toArray();
        $payload['foto_perfil'] = ImageManager::preferStoragePath($user->foto_perfil);

        return $payload;
    }

    private function splitFullName(string $fullName): array
    {
        $parts = preg_split('/\s+/', $fullName) ?: [];
        $parts = array_values(array_filter($parts));

        $nombre = $parts[0] ?? null;
        $apellidoPaterno = $parts[1] ?? null;
        $apellidoMaterno = count($parts) > 2 ? implode(' ', array_slice($parts, 2)) : null;

        return [$nombre, $apellidoPaterno, $apellidoMaterno];
    }
}
