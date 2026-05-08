<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PasaporteUsuario extends Model
{
    protected $table = 'pasaportes_usuario';

    protected $primaryKey = 'id_pasaporte';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'id_ruta',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function ruta(): BelongsTo
    {
        return $this->belongsTo(Ruta::class, 'id_ruta', 'id_ruta');
    }

    public function sellos(): HasMany
    {
        return $this->hasMany(PasaporteSello::class, 'id_pasaporte', 'id_pasaporte');
    }
}
