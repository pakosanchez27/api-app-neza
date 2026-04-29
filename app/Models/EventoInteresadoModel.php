<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventoInteresadoModel extends Model
{
    protected $table = 'evento_interesados';

    protected $fillable = [
        'evento_id',
        'visitor_id',
    ];

    public function evento()
    {
        return $this->belongsTo(EventoModel::class, 'evento_id');
    }
}
