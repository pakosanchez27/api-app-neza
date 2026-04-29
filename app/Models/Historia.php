<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Historia extends Model
{
    use SoftDeletes;

    protected $table = 'historias';

    protected $fillable = [
        'portada',
        'titulo',
        'slug',
        'autor',
        'resumen_corto',
        'periodo',
        'desarrollo',
        'fecha_publicacion',
        'estatus',
    ];

    protected $casts = [
        'fecha_publicacion' => 'date',
        'estatus' => 'integer',
    ];

    public function galeria(): HasMany
    {
        return $this->hasMany(HistoriaGaleria::class, 'historia_id')->orderBy('orden');
    }

    public function fuentes(): HasMany
    {
        return $this->hasMany(HistoriaFuente::class, 'historia_id')->orderBy('orden');
    }
}
