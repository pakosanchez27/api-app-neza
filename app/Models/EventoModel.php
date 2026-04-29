<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventoModel extends Model
{
    protected $table = 'events';

    protected $fillable = [
        'titulo',
        'portada',
        'fecha',
        'hora',
        'calle',
        'numero',
        'colonia',
        'latitud',
        'longitud',
        'acerca',
        'is_destacado',
        'estatus',
        'category_id',
        'user_id'
    ];

    public function categoria()
    {
        return $this->belongsTo(EventoCategoriasModel::class, 'category_id');
    }

    public function organizador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function interesados()
    {
        return $this->hasMany(EventoInteresadoModel::class, 'evento_id');
    }
}
