<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UsuarioCupon extends Model
{
    protected $table = 'usuarios_cupones';

    protected $fillable = [
        'user_id',
        'coupon_id',
        'status',
        'unique_code',
        'claimed_at',
        'redeemed_at',
        'expired_at',
    ];

    protected $casts = [
        'claimed_at' => 'datetime',
        'redeemed_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function cupon(): BelongsTo
    {
        return $this->belongsTo(Cupon::class, 'coupon_id');
    }

    public function redenciones(): HasMany
    {
        return $this->hasMany(RedencionCupon::class, 'user_coupon_id');
    }
}
