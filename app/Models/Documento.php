<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Documento extends Model
{
    protected $table = 'documentos';

    protected $primaryKey = 'id_documento';

    public $timestamps = true;

    protected $fillable = [
        'nombre_original',
        'nombre_guardado',
        'ruta_archivo',
        'id_tipo_documento',
    ];

    public function tipoDocumento(): BelongsTo
    {
        return $this->belongsTo(
            TipoDocumento::class,
            'id_tipo_documento',
            'id_tipo_documento'
        );
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'doc_usuarios',
            'id_documento',
            'user_id',
            'id_documento',
            'id'
        );
    }

    public function establecimientos(): BelongsToMany
    {
        return $this->belongsToMany(
            Establecimiento::class,
            'doc_establecimientos',
            'id_documento',
            'id_establecimiento',
            'id_documento',
            'id_establecimiento'
        );
    }
}
