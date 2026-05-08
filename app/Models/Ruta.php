<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruta extends Model
{
    protected $table = 'rutas';

    protected $primaryKey = 'id_ruta';

    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function establecimientos(): BelongsToMany
    {
        return $this->belongsToMany(
            Establecimiento::class,
            'ruta_establecimiento',
            'id_ruta',
            'id_establecimiento',
            'id_ruta',
            'id_establecimiento'
        )->withPivot(['orden'])->withTimestamps();
    }

    public function pasaportes(): HasMany
    {
        return $this->hasMany(PasaporteUsuario::class, 'id_ruta', 'id_ruta');
    }
}
