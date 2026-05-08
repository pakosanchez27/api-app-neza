<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveCommerceRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $payload = $this->input('payload');

        if (is_string($payload) && $payload !== '') {
            $decoded = json_decode($payload, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $this->merge([
                    'payload' => $decoded,
                ]);
            }
        }

        if ($this->has('finalize')) {
            $this->merge([
                'finalize' => filter_var($this->input('finalize'), FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'phase' => ['required', 'integer', 'between:1,7'],
            'finalize' => ['nullable', 'boolean'],
            'payload' => ['required', 'array'],
            'logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif,bmp,avif', 'max:5120'],
            'menu' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf'],
            'galeria' => ['nullable', 'array', 'max:5'],
            'galeria.*' => ['file', 'image', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'logo.uploaded' => 'No se pudo cargar el logo. Revisa el tamano del archivo o la configuracion del servidor.',
            'logo.mimes' => 'El logo debe estar en formato JPG, JPEG, PNG, WEBP, GIF, BMP o AVIF.',
            'logo.max' => 'El logo no debe pesar mas de 5 MB.',
            'menu.uploaded' => 'No se pudo cargar el menu. Revisa el tamano del archivo o la configuracion del servidor.',
            'menu.mimes' => 'El menu debe estar en formato JPG, JPEG, PNG o PDF.',
            'menu.max' => 'El menu excede el tamano permitido por el servidor.',
            'galeria.*.uploaded' => 'Una de las imagenes de la galeria no se pudo cargar.',
            'galeria.*.image' => 'Cada archivo de la galeria debe ser una imagen valida.',
            'galeria.*.max' => 'Cada imagen de la galeria no debe pesar mas de 5 MB.',
        ];
    }
}
