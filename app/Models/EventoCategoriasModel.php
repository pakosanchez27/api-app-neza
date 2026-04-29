<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventoCategoriasModel extends Model
{
    protected $table = 'event_categories';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
    ];

    public function eventos()
    {
        return $this->hasMany(EventoModel::class, 'category_id');
    }
}
