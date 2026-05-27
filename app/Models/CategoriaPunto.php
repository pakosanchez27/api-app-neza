<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaPunto extends Model
{
    protected $table = 'categorias_puntos';

    protected $fillable = [
        'tipo',
    ];

    public function puntosMapa()
    {
        return $this->hasMany(PuntoMapa::class, 'categoria_id');
    }
}
