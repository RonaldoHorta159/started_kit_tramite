<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Movimiento extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'movimientos';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'documento_id',
        'area_origen_id',
        'area_destino_id',
        'user_id',
        'proveido',
        'estado',
        'archivo_adjunto_path',
    ];

    /**
     * Los atributos que deben ser casteados.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'documento_id' => 'integer',
        'area_origen_id' => 'integer',
        'area_destino_id' => 'integer',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ======================================================================
    // RELACIONES
    // ======================================================================

    /**
     * Obtiene el documento al que pertenece este movimiento.
     */
    public function documento(): BelongsTo
    {
        return $this->belongsTo(Documento::class, 'documento_id');
    }

    /**
     * Obtiene el área de origen que envió el movimiento.
     */
    public function areaOrigen(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_origen_id');
    }

    /**
     * Obtiene el área de destino que recibió el movimiento.
     */
    public function areaDestino(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_destino_id');
    }

    /**
     * Obtiene el usuario que ejecutó el movimiento.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
