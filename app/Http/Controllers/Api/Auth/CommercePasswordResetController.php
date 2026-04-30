<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use App\Notifications\CommerceResetPasswordNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class CommercePasswordResetController extends Controller
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
            !$user->role ||
            $user->role->nombre !== 'AdminComercios'
        ) {
            return $this->genericLinkResponse();
        }

        if ((bool) $user->is_password_templ) {
            return response()->json([
                'message' => 'Tu cuenta aun usa una contrasena temporal. Revisa tu correo electronico y utiliza las instrucciones que recibiste para ingresar.',
            ]);
        }

        $token = Password::broker('users')->createToken($user);
        $user->notify(new CommerceResetPasswordNotification($token, $user->email));

        return $this->genericLinkResponse();
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::query()
            ->with('role')
            ->whereRaw('LOWER(email) = ?', [Str::lower($validated['email'])])
            ->first();

        if (
            !$user ||
            !$user->role ||
            $user->role->nombre !== 'AdminComercios'
        ) {
            return response()->json([
                'message' => 'El enlace de recuperacion no es valido o ya expiro.',
            ], 422);
        }

        $status = Password::broker('users')->reset(
            [
                'email' => $user->email,
                'password' => $validated['password'],
                'password_confirmation' => $validated['password_confirmation'],
                'token' => $validated['token'],
            ],
            function (User $resolvedUser, string $password): void {
                $resolvedUser->forceFill([
                    'password' => $password,
                    'is_password_templ' => false,
                    'remember_token' => Str::random(60),
                ])->save();

                $resolvedUser->tokens()->delete();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'El enlace de recuperacion no es valido o ya expiro.',
            ], 422);
        }

        return response()->json([
            'message' => 'Tu contrasena fue actualizada correctamente. Ya puedes iniciar sesion en tu cuenta de comercio.',
        ]);
    }

    private function genericLinkResponse(): JsonResponse
    {
        return response()->json([
            'message' => 'Si encontramos una cuenta de comercio asociada a ese correo, te enviaremos un enlace para restablecer tu contrasena.',
        ]);
    }
}
