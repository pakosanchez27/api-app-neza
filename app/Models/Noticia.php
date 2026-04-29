<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Noticia extends Model
{
    protected $table = 'noticias';

    protected $fillable = [
        'portada',
        'titulo',
        'subtitulo',
        'resumen',
        'galeria',
        'cta',
        'fecha_publicacion',
        'estatus',
    ];

    protected $casts = [
        'galeria' => 'array',
        'fecha_publicacion' => 'date',
        'estatus' => 'integer',
    ];
}
