<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contacto extends Model
{
    protected $table = 'contacto';

    protected $primaryKey = 'id_contacto';

    public $timestamps = true;

    protected $fillable = [
        'telefono',
        'tiktok',
        'instagram',
        'facebook',
        'correo',
        'id_establecimiento',
    ];

    public function establecimiento(): BelongsTo
    {
        return $this->belongsTo(
            Establecimiento::class,
            'id_establecimiento',
            'id_establecimiento'
        );
    }
}
