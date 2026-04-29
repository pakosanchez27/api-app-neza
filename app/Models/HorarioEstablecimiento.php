<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HorarioEstablecimiento extends Model
{
    protected $table = 'horario_establecimientos';

    protected $primaryKey = 'id_horario';

    public $timestamps = false;

    protected $fillable = [
        'dia_semana',
        'hora_apertura',
        'hora_cierra',
        'cerrado',
        'id_establecimiento',
    ];

    protected $casts = [
        'dia_semana' => 'integer',
        'cerrado' => 'boolean',
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
