<?php

namespace App\Http\Requests;

use App\Models\Preregistro;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePublicPreregistroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'telefono' => preg_replace('/\D+/', '', (string) $this->input('telefono', '')),
            'correo' => trim((string) $this->input('correo', '')),
            'nombre_est' => trim((string) $this->input('nombre_est', '')),
            'razon_social' => trim((string) $this->input('razon_social', '')),
            'descripcion_est' => trim((string) $this->input('descripcion_est', '')),
            'calle' => trim((string) $this->input('calle', '')),
            'numero' => trim((string) $this->input('numero', '')),
            'colonia' => trim((string) $this->input('colonia', '')),
            'codigo_postal' => trim((string) $this->input('codigo_postal', '')),
            'aviso_privacidad' => filter_var($this->input('aviso_privacidad'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
                ?? in_array((string) $this->input('aviso_privacidad'), ['1', 'on', 'yes'], true),
        ]);
    }

    public function rules(): array
    {
        $preregistro = $this->resolveCorrectionPreregistro();
        $isCorrection = $preregistro instanceof Preregistro;
        $correoRules = ['required', 'email', 'max:100'];

        if (! $isCorrection || strcasecmp((string) $preregistro->correo, (string) $this->input('correo')) !== 0) {
            $correoRules[] = 'unique:users,email';
        }

        return [
            'nombre_p' => ['required', 'string', 'max:50'],
            'app_p' => ['required', 'string', 'max:50'],
            'apm_p' => ['nullable', 'string', 'max:50'],
            'razon_social' => ['required', 'string', 'max:100'],
            'telefono' => ['required', 'digits:10'],
            'correo' => $correoRules,
            'nombre_est' => [
                'required',
                'string',
                'max:50',
                Rule::unique('preregistro', 'nombre_est')->ignore($preregistro?->id_preresgistro, 'id_preresgistro'),
            ],
            'tipo' => ['required', 'integer', 'exists:tipos,id_tipo'],
            'descripcion_est' => ['required', 'string', 'max:255'],
            'calle' => ['nullable', 'string', 'max:150'],
            'numero' => ['nullable', 'string', 'max:30'],
            'colonia' => ['nullable', 'string', 'max:150'],
            'codigo_postal' => ['nullable', 'string', 'max:10'],
            'latitud_us' => ['nullable', 'numeric', 'between:-90,90'],
            'longitud_us' => ['nullable', 'numeric', 'between:-180,180'],
            'aviso_privacidad' => ['accepted'],
            'ine' => array_filter([
                $isCorrection && $preregistro?->ine ? 'nullable' : 'required',
                'file',
                'mimes:pdf',
                'max:10240',
            ]),
            'lic_fun' => array_filter([
                $isCorrection && $preregistro?->lic_fun ? 'nullable' : 'required',
                'file',
                'mimes:pdf',
                'max:10240',
            ]),
            'foto_est' => array_filter([
                $isCorrection && $preregistro?->foto_est ? 'nullable' : 'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:10240',
            ]),
        ];
    }

    public function messages(): array
    {
        return [
            'correo.unique' => 'Ese correo electronico ya esta registrado en usuarios.',
            'nombre_est.unique' => 'Ya existe un preregistro con ese nombre comercial.',
            'tipo.exists' => 'Selecciona un tipo de comercio valido.',
            'aviso_privacidad.accepted' => 'Debes aceptar el aviso de privacidad.',
            'ine.mimes' => 'La identificacion oficial debe estar en formato PDF.',
            'lic_fun.mimes' => 'La licencia o documento de funcionamiento debe estar en formato PDF.',
            'foto_est.image' => 'La foto del establecimiento debe ser una imagen valida.',
            'foto_est.mimes' => 'La foto del establecimiento debe estar en formato JPG, JPEG, PNG o WEBP.',
            'ine.max' => 'La identificacion oficial no debe pesar mas de 10 MB.',
            'lic_fun.max' => 'La licencia o documento de funcionamiento no debe pesar mas de 10 MB.',
            'foto_est.max' => 'La foto del establecimiento no debe pesar mas de 10 MB.',
        ];
    }

    private function resolveCorrectionPreregistro(): ?Preregistro
    {
        $token = (string) $this->route('token', '');

        if ($token === '') {
            return null;
        }

        return Preregistro::query()
            ->where('token_correccion', $token)
            ->first();
    }
}
