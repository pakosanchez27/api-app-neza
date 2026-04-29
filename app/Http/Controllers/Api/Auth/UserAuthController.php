<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserAuthController extends Controller
{
    public function login(UserLoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $user = User::query()
            ->with('role')
            ->where('email', $credentials['email'])
            ->first();

        if (!$user || !Hash::check($credentials['password'], $user->password ?? '')) {
            return response()->json([
                'message' => 'Las credenciales proporcionadas no son validas.',
            ], 422);
        }

        if (!$user->activo) {
            return response()->json([
                'message' => 'La cuenta aun no esta activa. Revisa tu correo electronico.',
            ], 403);
        }

        if ($user->role && $user->role->nombre === 'AdminComercios') {
            return response()->json([
                'message' => 'Este acceso es exclusivo para usuarios generales de NezaGo.',
            ], 403);
        }

        $user->tokens()->delete();
        $token = $user->createToken('app-user')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesion exitoso.',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'nombre_p' => $user->nombre_p,
                'app_p' => $user->app_p,
                'apm_p' => $user->apm_p,
                'email' => $user->email,
                'telefono' => $user->telefono,
                'foto_perfil' => $user->foto_perfil,
                'role' => $user->role?->nombre,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('role');

        return response()->json($user);
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
            'full_name' => ['required', 'string', 'max:180'],
            'email' => [
                'required',
                'email',
                'max:80',
                Rule::unique('users', 'email')->ignore($user->id, 'id'),
            ],
            'telefono' => ['nullable', 'string', 'max:20'],
            'foto_perfil' => ['nullable', 'image'],
            'current_password' => ['nullable', 'string'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (!empty($validated['new_password'])) {
            if (empty($validated['current_password'])) {
                return response()->json([
                    'message' => 'Debes escribir tu contraseña actual.',
                ], 422);
            }

            if (!Hash::check($validated['current_password'], $user->password ?? '')) {
                return response()->json([
                    'message' => 'La contraseña actual no es correcta.',
                ], 422);
            }
        }

        $fullName = trim($validated['full_name']);
        [$nombre, $apellidoPaterno, $apellidoMaterno] = $this->splitFullName($fullName);

        $updates = [
            'name' => $fullName,
            'nombre_p' => $nombre,
            'app_p' => $apellidoPaterno,
            'apm_p' => $apellidoMaterno,
            'email' => $validated['email'],
            'telefono' => !empty($validated['telefono'])
                ? preg_replace('/\D+/', '', $validated['telefono'])
                : null,
        ];

        if ($request->hasFile('foto_perfil')) {
            if ($user->foto_perfil) {
                Storage::disk('public')->delete($user->foto_perfil);
            }

            $updates['foto_perfil'] = $request->file('foto_perfil')->store(
                "user-profile/{$user->id}",
                'public'
            );
        }

        if (!empty($validated['new_password'])) {
            $updates['password'] = $validated['new_password'];
            $updates['is_password_templ'] = false;
        }

        $user->forceFill($updates)->save();
        $user->refresh()->load('role');

        return response()->json([
            'message' => 'Perfil actualizado correctamente.',
            'user' => $user,
        ]);
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
