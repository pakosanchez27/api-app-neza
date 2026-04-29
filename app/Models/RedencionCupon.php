<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RedencionCupon extends Model
{
    protected $table = 'redenciones_cupones';

    protected $fillable = [
        'user_coupon_id',
        'id_establecimiento',
        'redeemed_by_user_id',
        'redeemed_at',
        'notes',
    ];

    protected $casts = [
        'redeemed_at' => 'datetime',
    ];

    public function usuarioCupon(): BelongsTo
    {
        return $this->belongsTo(UsuarioCupon::class, 'user_coupon_id');
    }

    public function establecimiento(): BelongsTo
    {
        return $this->belongsTo(Establecimiento::class, 'id_establecimiento', 'id_establecimiento');
    }

    public function redimidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'redeemed_by_user_id', 'id');
    }
}
