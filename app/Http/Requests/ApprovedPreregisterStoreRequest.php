<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApprovedPreregisterStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'folio_preregistro' => ['nullable', 'string', 'max:100'],

            'solicitante' => ['required', 'array'],
            'solicitante.nombre' => ['required', 'string', 'max:60'],
            'solicitante.apellido_p' => ['required', 'string', 'max:60'],
            'solicitante.apellido_m' => ['nullable', 'string', 'max:60'],
            'solicitante.telefono' => ['nullable', 'string', 'max:15'],
            'solicitante.email' => ['required', 'email', 'max:80'],

            'establecimiento' => ['required', 'array'],
            'establecimiento.nombre_comercial' => ['required', 'string', 'max:50'],
            'establecimiento.razon_social' => ['nullable', 'string', 'max:200'],
            'establecimiento.tipo_id' => ['required', 'integer', 'exists:tipos,id_tipo'],
            'establecimiento.descripcion' => ['nullable', 'string'],
            'establecimiento.is_route' => ['nullable', 'boolean'],

            'ubicacion' => ['nullable', 'array'],
            'ubicacion.latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'ubicacion.longitud' => ['nullable', 'numeric', 'between:-180,180'],
            'ubicacion.calle' => ['nullable', 'string', 'max:45'],
            'ubicacion.colonia' => ['nullable', 'string', 'max:45'],
            'ubicacion.num_int' => ['nullable', 'string', 'max:45'],
            'ubicacion.num_ext' => ['nullable', 'string', 'max:45'],
            'ubicacion.localidad' => ['nullable', 'string', 'max:45'],
            'ubicacion.cp' => ['nullable', 'string', 'max:45'],
            'ubicacion.referencias' => ['nullable', 'string', 'max:255'],

            'documentos' => ['nullable', 'array'],
            'documentos.ine' => ['nullable', 'string', 'max:500'],
            'documentos.licencia_funcionamiento' => ['nullable', 'string', 'max:500'],
            'documentos.foto_establecimiento' => ['nullable', 'string', 'max:500'],
        ];
    }
}
