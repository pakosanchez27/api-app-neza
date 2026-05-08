<?php

namespace App\Models;

use Database\Factories\UserFactory;
use App\Notifications\UserResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'nombre_p',
        'app_p',
        'apm_p',
        'email',
        'telefono',
        'email_verified_at',
        'is_password_templ',
        'password',
        'ultimo_acceso',
        'estatus',
        'activo',
        'token_activacion',
        'foto_perfil',
        'id_rol',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'token_activacion',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_password_templ' => 'boolean',
            'activo' => 'boolean',
            'ultimo_acceso' => 'date',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'id_rol', 'id_rol');
    }

    public function establecimientos(): HasMany
    {
        return $this->hasMany(Establecimiento::class, 'user_id', 'id');
    }

    public function usuariosCupones(): HasMany
    {
        return $this->hasMany(UsuarioCupon::class, 'user_id', 'id');
    }

    public function pasaportes(): HasMany
    {
        return $this->hasMany(PasaporteUsuario::class, 'user_id', 'id');
    }

    public function documentos(): BelongsToMany
    {
        return $this->belongsToMany(
            Documento::class,
            'doc_usuarios',
            'user_id',
            'id_documento',
            'id',
            'id_documento'
        );
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new UserResetPasswordNotification($token, $this->email));
    }
}
