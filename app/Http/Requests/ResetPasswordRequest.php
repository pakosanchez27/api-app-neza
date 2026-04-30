<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'max:80'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => 'El enlace de recuperacion no es valido.',
            'email.required' => 'Escribe tu correo electronico.',
            'email.email' => 'Escribe un correo electronico valido.',
            'email.max' => 'El correo electronico no puede tener mas de 80 caracteres.',
            'password.required' => 'Escribe tu nueva contrasena.',
            'password.min' => 'La contrasena debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmacion de la contrasena no coincide.',
            'password_confirmation.required' => 'Confirma tu nueva contrasena.',
        ];
    }
}
