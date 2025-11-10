<?php

namespace App\Models;

use App\Enums\Estado;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'codigo', 'estado'];

    protected $casts = [
        'estado' => Estado::class,
    ];

    /** Usuarios que tienen esta área como adicional (pivot area_user) */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'area_user', 'area_id', 'user_id')
            ->withTimestamps();
    }

    /** Usuarios que tienen esta área como principal (users.primary_area_id) */
    public function primaryUsers(): HasMany
    {
        return $this->hasMany(User::class, 'primary_area_id');
    }

    /**
     * Scope para aplicar filtros y ordenamiento dinámico.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $filters
     * @param  array  $sort
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterAndSort($query, array $filters, array $sort)
    {
        $query->when($filters['nombre'] ?? null, function ($q, $nombre) {
            $q->where('nombre', 'like', '%'.$nombre.'%');
        })
        ->when($filters['codigo'] ?? null, function ($q, $codigo) {
            $q->where('codigo', 'like', '%'.$codigo.'%');
        })
        ->when($filters['estado'] ?? null, function ($q, $estado) {
            $q->whereIn('estado', (array) $estado);
        });

        if (!empty($sort['field'])) {
            $query->orderBy($sort['field'], $sort['direction'] ?? 'asc');
        }

        return $query;
    }
}
