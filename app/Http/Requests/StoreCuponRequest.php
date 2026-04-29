<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCuponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'discount_type' => ['required', 'in:fixed,percentage'],
            'discount_value' => ['required', 'numeric', 'min:0.01'],
            'stock' => ['required', 'integer', 'min:1'],
            'claim_limit_per_user' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'terms' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
