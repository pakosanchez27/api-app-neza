<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Amenidad extends Model
{
    protected $table = 'amenidades';

    protected $primaryKey = 'id_amenidades';

    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function establecimientos(): BelongsToMany
    {
        return $this->belongsToMany(
            Establecimiento::class,
            'est_amenidades',
            'id_amenidades',
            'id_establecimiento',
            'id_amenidades',
            'id_establecimiento'
        );
    }
}
