<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Establecimiento extends Model
{
    protected $table = 'establecimientos';

    protected $primaryKey = 'id_establecimiento';

    public $timestamps = true;

    protected $fillable = [
        'nombre_est',
        'menu',
        'aforo',
        'logo',
        'user_id',
        'id_tipo',
        'descripcion',
        'is_route',
        'is_visible',
        'estatus',
        'razon_social',
        'qr_token',
    ];

    protected $casts = [
        'estatus' => 'boolean',
        'is_route' => 'boolean',
        'is_visible' => 'boolean',
        'aforo' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Establecimiento $establecimiento) {
            if (!empty($establecimiento->qr_token)) {
                return;
            }

            $establecimiento->qr_token = self::generateQrToken();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(Tipo::class, 'id_tipo', 'id_tipo');
    }

    public function contacto(): HasOne
    {
        return $this->hasOne(Contacto::class, 'id_establecimiento', 'id_establecimiento');
    }

    public function domicilio(): HasOne
    {
        return $this->hasOne(Domicilio::class, 'id_establecimiento', 'id_establecimiento');
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(HorarioEstablecimiento::class, 'id_establecimiento', 'id_establecimiento');
    }

    public function cupones(): HasMany
    {
        return $this->hasMany(Cupon::class, 'id_establecimiento', 'id_establecimiento');
    }

    public function amenidades(): BelongsToMany
    {
        return $this->belongsToMany(
            Amenidad::class,
            'est_amenidades',
            'id_establecimiento',
            'id_amenidades',
            'id_establecimiento',
            'id_amenidades'
        );
    }

    public function documentos(): BelongsToMany
    {
        return $this->belongsToMany(
            Documento::class,
            'doc_establecimientos',
            'id_establecimiento',
            'id_documento',
            'id_establecimiento',
            'id_documento'
        );
    }

    public function rutas(): BelongsToMany
    {
        return $this->belongsToMany(
            Ruta::class,
            'ruta_establecimiento',
            'id_establecimiento',
            'id_ruta',
            'id_establecimiento',
            'id_ruta'
        )->withPivot(['orden'])->withTimestamps();
    }

    public function pasaporteSellos(): HasMany
    {
        return $this->hasMany(PasaporteSello::class, 'id_establecimiento', 'id_establecimiento');
    }

    private static function generateQrToken(): string
    {
        do {
            $candidate = sprintf('NEZA-QR-%s', Str::upper(Str::random(20)));
        } while (self::query()->where('qr_token', $candidate)->exists());

        return $candidate;
    }
}
