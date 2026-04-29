<?php

namespace App\Http\Controllers;

use App\Mail\UserActivationMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate(
            [
                'nombre_p' => 'required|string|max:60',
                'app_p' => 'required|string|max:60',
                'apm_p' => 'nullable|string|max:60',
                'telefono' => 'required|string|size:10|unique:users,telefono',
                'email' => 'required|string|email|max:80|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
            ],
            [
                'nombre_p.required' => 'El nombre es obligatorio.',
                'nombre_p.string' => 'El nombre debe ser un texto valido.',
                'nombre_p.max' => 'El nombre no puede tener mas de 60 caracteres.',
                'app_p.required' => 'El apellido paterno es obligatorio.',
                'app_p.string' => 'El apellido paterno debe ser un texto valido.',
                'app_p.max' => 'El apellido paterno no puede tener mas de 60 caracteres.',
                'apm_p.string' => 'El apellido materno debe ser un texto valido.',
                'apm_p.max' => 'El apellido materno no puede tener mas de 60 caracteres.',
                'telefono.required' => 'El telefono es obligatorio.',
                'telefono.string' => 'El telefono debe ser un texto valido.',
                'telefono.size' => 'El telefono debe tener exactamente 10 digitos.',
                'telefono.unique' => 'El telefono ya esta registrado.',
                'email.required' => 'El correo electronico es obligatorio.',
                'email.string' => 'El correo electronico debe ser un texto valido.',
                'email.email' => 'El correo electronico no tiene un formato valido.',
                'email.max' => 'El correo electronico no puede tener mas de 80 caracteres.',
                'email.unique' => 'El correo electronico ya esta registrado.',
                'password.required' => 'La contrasena es obligatoria.',
                'password.string' => 'La contrasena debe ser un texto valido.',
                'password.min' => 'La contrasena debe tener al menos 8 caracteres.',
                'password.confirmed' => 'La confirmacion de la contrasena no coincide.',
            ]
        );

        try {
            $user = DB::transaction(function () use ($validatedData) {
                $activationToken = Str::random(64);

                $user = new User();
                $user->name = trim(implode(' ', array_filter([
                    $validatedData['nombre_p'],
                    $validatedData['app_p'],
                    $validatedData['apm_p'] ?? null,
                ])));
                $user->nombre_p = $validatedData['nombre_p'];
                $user->app_p = $validatedData['app_p'];
                $user->apm_p = $validatedData['apm_p'] ?? null;
                $user->telefono = $validatedData['telefono'];
                $user->email = $validatedData['email'];
                $user->password = $validatedData['password'];
                $user->id_rol = 3; // Rol de cliente
                $user->activo = false;
                $user->email_verified_at = null;
                $user->token_activacion = $activationToken;
                $user->foto_perfil = '';
                $user->save();


                Mail::to($user->email)->send(new UserActivationMail($user));

                return $user;
            });
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'No fue posible completar el registro. Intenta nuevamente en unos minutos.',
            ], 500);
        }

        return response()->json([
            'message' => 'Usuario registrado exitosamente. Revisa tu correo para activar la cuenta.',
            'user' => $user,
            'code' => 201,
        ], 201);
    }

    public function activate(string $token)
    {
        $user = User::query()
            ->where('token_activacion', $token)
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'El enlace de activacion no es valido o ya fue utilizado.',
            ], 404);
        }

        $user->forceFill([
            'email_verified_at' => now(),
            'activo' => true,
            'token_activacion' => null,
        ])->save();

        return response()->json([
            'message' => 'Tu cuenta fue activada correctamente. Ya puedes iniciar sesion.',
        ]);
    }
}
