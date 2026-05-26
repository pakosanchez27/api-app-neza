<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Preregistro extends Model
{
    protected $table = 'preregistro';

    protected $primaryKey = 'id_preresgistro';

    public $timestamps = true;

    protected $fillable = [
        'nombre_p',
        'app_p',
        'apm_p',
        'razon_social',
        'telefono',
        'correo',
        'nombre_est',
        'tipo',
        'descripcion_est',
        'lic_fun',
        'ine',
        'latitud',
        'longitud',
        'estatus_registro',
        'observacion_registro',
        'aviso_privacidad',
        'foto_est',
    ];
}
