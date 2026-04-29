<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimelineModel extends Model
{
    protected $table = 'timelines';
    protected $fillable = [
        'lugar_turistico',
        'descripcion',
        'imagen_antes',
        'imagen_despues',
        'orden',
        'estatus',
    ];
}
