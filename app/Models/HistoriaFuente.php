<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriaFuente extends Model
{
    protected $table = 'historia_fuentes';

    protected $fillable = [
        'historia_id',
        'titulo',
        'descripcion',
        'url',
        'orden',
    ];

    public function historia(): BelongsTo
    {
        return $this->belongsTo(Historia::class, 'historia_id');
    }
}
