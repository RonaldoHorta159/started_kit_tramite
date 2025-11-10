<?php

namespace App\Models;

use App\Enums\Estado;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoDocumento extends Model
{
    use HasFactory;

    /** Tabla explícita (evita 'tipo_documentos' por convención) */
    protected $table = 'tipos_documento';

    protected $fillable = ['nombre', 'estado'];

    protected $casts = [
        'estado' => Estado::class,
    ];

    /** Relación con documentos (FK: documentos.tipo_documento_id) */
    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'tipo_documento_id');
    }

    /** Scope útil si filtras por estado activo */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'Activo');
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
        $query->when($filters['q'] ?? null, function ($q, $search) {
            $q->where('nombre', 'like', '%'.$search.'%');
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
