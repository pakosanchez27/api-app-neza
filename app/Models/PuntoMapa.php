<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PuntoMapa extends Model
{
    protected $table = 'puntos_mapa';

    protected $fillable = [
        'nombre_punto',
        'descripcion',
        'foto_principal',
        'categoria_id',
        'calle',
        'numero_exterior',
        'numero_interior',
        'cp',
        'colonia',
        'latitud',
        'longitud',
        'telefono',
        'email',
        'horarios',
        'estatus',
    ];

    protected $casts = [
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
        'estatus' => 'integer',
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaPunto::class, 'categoria_id');
    }
}
