<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute; // 👈 Importar la clase Attribute
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        // ✅ Se añaden todos los campos de la migración
        'dni',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'email',
        'celular',
        'password',
        'foto_path',
        'primary_area_id',
        'rol',
        'estado',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // Se elimina 'email_verified_at' porque ya no existe
            'password' => 'hashed',
        ];
    }

    /**
     * ✅ Accessor para generar el atributo 'name' dinámicamente.
     *
     * Esto permite que el código existente que use $user->name
     * siga funcionando sin errores.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn() => "{$this->nombres} {$this->apellido_paterno} {$this->apellido_materno}",
        );
    }
}
