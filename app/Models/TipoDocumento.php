<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoDocumento extends Model
{
    use HasFactory;

    /** Tabla explícita (evita 'tipo_documentos' por convención) */
    protected $table = 'tipos_documento';

    protected $fillable = ['nombre', 'estado'];

    /** Opcional: constantes para consistencia */
    public const ESTADOS = ['Activo', 'Inactivo'];

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
}
