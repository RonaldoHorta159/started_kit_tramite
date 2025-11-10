<?php

namespace App\Models;

use App\Enums\Estado;
use App\Enums\Rol;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
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

    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'estado' => Estado::class,
            'rol' => Rol::class,
        ];
    }

    // ✅ nombre completo (como ya lo tienes)
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn() => "{$this->nombres} {$this->apellido_paterno} {$this->apellido_materno}",
        );
    }

    // ✅ Área primaria (FK: users.primary_area_id -> areas.id)
    public function primaryArea(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'primary_area_id');
    }

    // (Opcional) Dejar lista la relación para múltiples áreas vía pivot `area_user`
    // Asegúrate de que el pivot use `user_id` apuntando a la tabla `users`
    public function areas(): BelongsToMany
    {
        return $this->belongsToMany(Area::class, 'area_user', 'user_id', 'area_id')
            ->withTimestamps();
    }

    /**
     * Scope para aplicar filtros y ordenamiento dinámico.
     */
    public function scopeFilterAndSort($query, array $filters, array $sort)
    {
        $query->when($filters['q'] ?? null, function ($q, $search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('dni', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('nombres', 'like', '%'.$search.'%')
                    ->orWhere('apellido_paterno', 'like', '%'.$search.'%')
                    ->orWhere('apellido_materno', 'like', '%'.$search.'%');
            });
        })
        ->when($filters['estado'] ?? null, function ($q, $estado) {
            $q->whereIn('estado', (array) $estado);
        })
        ->when($filters['rol'] ?? null, function ($q, $rol) {
            $q->whereIn('rol', (array) $rol);
        })
        ->when($filters['area_id'] ?? null, function ($q, $areaId) {
            $q->where(function ($q2) use ($areaId) {
                $q2->where('primary_area_id', $areaId)
                    ->orWhereHas('areas', function ($q3) use ($areaId) {
                        $q3->where('areas.id', $areaId);
                    });
            });
        });

        if (!empty($sort['field'])) {
            $query->orderBy($sort['field'], $sort['direction'] ?? 'asc');
        }

        return $query;
    }
}
