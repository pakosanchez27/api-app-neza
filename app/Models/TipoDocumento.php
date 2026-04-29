<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoDocumento extends Model
{
    protected $table = 'tipo_documentos';

    protected $primaryKey = 'id_tipo_documento';

    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'id_tipo_documento', 'id_tipo_documento');
    }
}
