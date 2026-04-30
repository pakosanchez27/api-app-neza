<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserPasswordResetController extends Controller
{
    public function sendResetLink(ForgotPasswordRequest $request): JsonResponse
    {
        $email = Str::lower($request->validated('email'));

        $user = User::query()
            ->with('role')
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if (
            !$user ||
            ($user->role && $user->role->nombre === 'AdminComercios')
        ) {
            return $this->genericLinkResponse();
        }

        Password::broker('users')->sendResetLink([
            'email' => $user->email,
        ]);

        return $this->genericLinkResponse();
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $status = Password::broker('users')->reset(
            [
                'email' => $validated['email'],
                'password' => $validated['password'],
                'password_confirmation' => $validated['password_confirmation'],
                'token' => $validated['token'],
            ],
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => $password,
                    'is_password_templ' => false,
                    'remember_token' => Str::random(60),
                ])->save();

                $user->tokens()->delete();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'El enlace de recuperacion no es valido o ya expiro.',
            ], 422);
        }

        return response()->json([
            'message' => 'Tu contrasena fue actualizada correctamente. Ya puedes iniciar sesion.',
        ]);
    }

    private function genericLinkResponse(): JsonResponse
    {
        return response()->json([
            'message' => 'Si encontramos una cuenta asociada a ese correo, te enviaremos un enlace para restablecer tu contrasena.',
        ]);
    }
}
