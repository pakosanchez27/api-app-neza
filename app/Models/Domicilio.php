<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Domicilio extends Model
{
    protected $table = 'domicilios';

    protected $primaryKey = 'id_domicilio';

    public $timestamps = true;

    protected $fillable = [
        'calle',
        'colonia',
        'num_int',
        'num_ext',
        'x',
        'y',
        'localidad',
        'cp',
        'latitud',
        'longitud',
        'id_establecimiento',
        'referencias',
    ];

    protected $casts = [
        'x' => 'decimal:6',
        'y' => 'decimal:6',
        'latitud' => 'decimal:6',
        'longitud' => 'decimal:6',
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
