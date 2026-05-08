<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasaporteSello extends Model
{
    protected $table = 'pasaporte_sellos';

    protected $primaryKey = 'id_pasaporte_sello';

    public $timestamps = true;

    protected $fillable = [
        'id_pasaporte',
        'id_establecimiento',
        'qr_token_usado',
        'sealed_at',
    ];

    protected $casts = [
        'sealed_at' => 'datetime',
    ];

    public function pasaporte(): BelongsTo
    {
        return $this->belongsTo(PasaporteUsuario::class, 'id_pasaporte', 'id_pasaporte');
    }

    public function establecimiento(): BelongsTo
    {
        return $this->belongsTo(Establecimiento::class, 'id_establecimiento', 'id_establecimiento');
    }
}
