<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriaGaleria extends Model
{
    protected $table = 'historia_galeria';

    protected $fillable = [
        'historia_id',
        'imagen',
        'orden',
    ];

    public function historia(): BelongsTo
    {
        return $this->belongsTo(Historia::class, 'historia_id');
    }
}
