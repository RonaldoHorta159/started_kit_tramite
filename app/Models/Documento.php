<?php

namespace App\Models;

use App\Enums\EstadoDocumento;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Documento extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'documentos';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'parent_id',
        'codigo_unico',
        'nro_documento',
        'correlativo_area',
        'correlativo_tipo',
        'asunto',
        'folios',
        'prioridad',
        'archivo_path',
        'tipo_documento_id',
        'user_id',
        'area_origen_id',
        'area_actual_id',
        'estado',
    ];

    /**
     * Los atributos que deben ser casteados.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'folios' => 'integer',
        'correlativo_area' => 'integer',
        'correlativo_tipo' => 'integer',
        'tipo_documento_id' => 'integer',
        'user_id' => 'integer',
        'area_origen_id' => 'integer',
        'area_actual_id' => 'integer',
        'anio_creacion' => 'integer', // Castear la columna generada
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'estado' => EstadoDocumento::class,
    ];

    // ======================================================================
    // RELACIONES
    // ======================================================================

    /**
     * Obtiene el usuario que creó el documento[cite: 1568].
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Obtiene el tipo de documento[cite: 1569].
     */
    public function tipoDocumento(): BelongsTo
    {
        return $this->belongsTo(TipoDocumento::class, 'tipo_documento_id');
    }

    /**
     * Obtiene el área de origen que emitió el documento[cite: 1570].
     */
    public function areaOrigen(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_origen_id');
    }

    /**
     * Obtiene el área que tiene actualmente el documento[cite: 1571].
     */
    public function areaActual(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_actual_id');
    }

    /**
     * Obtiene todos los movimientos (historial) del documento[cite: 1573].
     */
    public function movimientos(): HasMany
    {
        return $this->hasMany(Movimiento::class, 'documento_id');
    }

    /**
     * Obtiene el documento padre (si es una respuesta)[cite: 1574].
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Documento::class, 'parent_id');
    }

    /**
     * Obtiene los documentos hijos (respuestas)[cite: 1574].
     */
    public function children(): HasMany
    {
        return $this->hasMany(Documento::class, 'parent_id');
    }

    // ======================================================================
    // SCOPES (ÁMBITOS DE CONSULTA)
    // ======================================================================

    /**
     * Scope para la "Bandeja de Entrada" de un área específica[cite: 1576].
     */
    public function scopeForInbox(Builder $query, int $areaId): void
    {
        $query->where('area_actual_id', $areaId);
    }

    /**
     * Scope para la "Gestión de Documentos" (emitidos) de un usuario[cite: 1578].
     */
    public function scopeCreatedBy(Builder $query, int $userId): void
    {
        $query->where('user_id', $userId);
    }

    // ======================================================================
    // ACCESSORS (ACCESORES)
    // ======================================================================

    /**
     * Verifica si el documento tiene estado 'Archivado'[cite: 1579].
     */
    public function getIsArchivadoAttribute(): bool
    {
        return $this->estado === 'Archivado';
    }
}
