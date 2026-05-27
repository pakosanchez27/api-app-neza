<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Preregistro extends Model
{
    public const ESTATUS_PENDIENTE = 0;

    public const ESTATUS_ACEPTADO = 1;

    public const ESTATUS_REQUIERE_CORRECCION = 2;

    public const ESTATUS_RECHAZADO_DEFINITIVO = 3;

    protected $table = 'preregistro';

    protected $primaryKey = 'id_preresgistro';

    public $timestamps = true;

    protected $fillable = [
        'nombre_p',
        'app_p',
        'apm_p',
        'razon_social',
        'telefono',
        'correo',
        'nombre_est',
        'tipo',
        'descripcion_est',
        'calle',
        'numero',
        'colonia',
        'codigo_postal',
        'lic_fun',
        'ine',
        'latitud',
        'longitud',
        'estatus_registro',
        'observacion_registro',
        'aviso_privacidad',
        'foto_est',
        'token_correccion',
        'token_correccion_expira_en',
    ];

    protected $casts = [
        'token_correccion_expira_en' => 'datetime',
        'aviso_privacidad' => 'boolean',
    ];

    public function tipoRelacion(): BelongsTo
    {
        return $this->belongsTo(Tipo::class, 'tipo', 'id_tipo');
    }

    public function estatusEtiqueta(): string
    {
        return match ((int) ($this->estatus_registro ?? self::ESTATUS_PENDIENTE)) {
            self::ESTATUS_PENDIENTE => 'Pendiente',
            self::ESTATUS_ACEPTADO => 'Aceptado',
            self::ESTATUS_REQUIERE_CORRECCION => 'Requiere correccion',
            self::ESTATUS_RECHAZADO_DEFINITIVO => 'Rechazado definitivo',
            default => (string) $this->estatus_registro,
        };
    }

    public function permiteRevision(): bool
    {
        return in_array((int) ($this->estatus_registro ?? self::ESTATUS_PENDIENTE), [
            self::ESTATUS_PENDIENTE,
            self::ESTATUS_REQUIERE_CORRECCION,
        ], true);
    }

    public function puedeCorregirseConToken(?string $token = null): bool
    {
        if ((int) ($this->estatus_registro ?? self::ESTATUS_PENDIENTE) !== self::ESTATUS_REQUIERE_CORRECCION) {
            return false;
        }

        if (! $this->token_correccion || ! $this->token_correccion_expira_en) {
            return false;
        }

        if ($token !== null && $this->token_correccion !== $token) {
            return false;
        }

        return $this->token_correccion_expira_en->isFuture();
    }
}
