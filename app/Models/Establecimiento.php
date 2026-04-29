<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
    ];

    protected $casts = [
        'estatus' => 'boolean',
        'is_route' => 'boolean',
        'is_visible' => 'boolean',
        'aforo' => 'integer',
    ];

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
}
