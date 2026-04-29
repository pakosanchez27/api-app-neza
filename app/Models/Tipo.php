<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tipo extends Model
{
    protected $table = 'tipos';

    protected $primaryKey = 'id_tipo';

    public $timestamps = true;

    protected $fillable = [
        'nombre',
    ];

    public function establecimientos(): HasMany
    {
        return $this->hasMany(Establecimiento::class, 'id_tipo', 'id_tipo');
    }
}
