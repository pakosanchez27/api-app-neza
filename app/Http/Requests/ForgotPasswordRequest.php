<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:80'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Escribe tu correo electronico.',
            'email.email' => 'Escribe un correo electronico valido.',
            'email.max' => 'El correo electronico no puede tener mas de 80 caracteres.',
        ];
    }
}
