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
            'logo' => ['nullable', 'file', 'image'],
            'menu' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf'],
            'galeria' => ['nullable', 'array', 'max:5'],
            'galeria.*' => ['file', 'image', 'max:5120'],
        ];
    }
}
