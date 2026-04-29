<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cupon extends Model
{
    protected $table = 'cupones';

    protected $fillable = [
        'id_establecimiento',
        'title',
        'description',
        'discount_type',
        'discount_value',
        'stock',
        'claim_limit_per_user',
        'starts_at',
        'expires_at',
        'terms',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'stock' => 'integer',
        'claim_limit_per_user' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function establecimiento(): BelongsTo
    {
        return $this->belongsTo(Establecimiento::class, 'id_establecimiento', 'id_establecimiento');
    }

    public function usuariosCupones(): HasMany
    {
        return $this->hasMany(UsuarioCupon::class, 'coupon_id');
    }
}
