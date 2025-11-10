<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use App\Models\CorrelativoTipoAnual;

class CorrelativeService
{
    /**
     * "Echa un vistazo" al siguiente número correlativo sin incrementarlo.
     * Ideal para mostrar en el frontend.
     *
     * @param int $tipoDocumentoId
     * @param int|null $anio (Opcional)
     * @return int
     */
    public function peekNextNumber(int $tipoDocumentoId, ?int $anio = null): int
    {
        // Usa la zona horaria de la app
        $yr = $anio ?? Carbon::now(config('app.timezone'))->year;

        $correlativo = CorrelativoTipoAnual::where('tipo_documento_id', $tipoDocumentoId)
            ->where('anio', $yr)
            ->first();

        return ($correlativo ? $correlativo->ultimo_correlativo : 0) + 1;
    }

    /**
     * Genera y asigna el siguiente número correlativo de forma segura.
     * Código robusto que maneja "race conditions" (Error 1062) y "deadlocks" (Error 1213)
     * [cite_start]con reintentos [cite: 1661-1662, 1664-1665].
     *
     * @param int $tipoDocumentoId
     * @param int|null $anio (Opcional)
     * @return int El nuevo número correlativo generado
     */
    public function generateNextNumber(int $tipoDocumentoId, ?int $anio = null): int
    {
        // Usa la zona horaria de la app
        $yr = $anio ?? Carbon::now(config('app.timezone'))->year;

        $attempts = 0;

        // Etiqueta para el reintento en caso de deadlock
        inicio:
        try {
            return DB::transaction(function () use ($tipoDocumentoId, $yr) {

                // 1. Intenta obtener la fila y bloquearla
                $row = CorrelativoTipoAnual::where('tipo_documento_id', $tipoDocumentoId)
                    ->where('anio', $yr)
                    ->lockForUpdate() // Bloqueo pesimista
                    ->first();

                if (!$row) {
                    // 2. La fila no existe. Intenta crearla.
                    try {
                        $row = CorrelativoTipoAnual::create([
                            'tipo_documento_id' => $tipoDocumentoId,
                            'anio' => $yr,
                            'ultimo_correlativo' => 0,
                        ]);
                        // Vuelve a obtenerla con bloqueo para asegurar consistencia
                        $row = CorrelativoTipoAnual::whereKey($row->id)->lockForUpdate()->first();

                    } catch (QueryException $e) {
                        // 3. ¡Condición de carrera! Otro proceso la creó justo ahora (Error 1062).
                        // No hay problema, simplemente la volvemos a seleccionar con bloqueo.
                        if (str_contains($e->getMessage(), '1062') || str_contains($e->getMessage(), '23505')) {
                            $row = CorrelativoTipoAnual::where('tipo_documento_id', $tipoDocumentoId)
                                ->where('anio', $yr)
                                ->lockForUpdate()
                                ->first();
                        } else {
                            // Fue otro error, lo lanzamos
                            throw $e;
                        }
                    }
                }

                // 4. Incrementa, guarda y devuelve el nuevo número
                $row->ultimo_correlativo++;
                $row->save();
                return (int) $row->ultimo_correlativo;
            });
        } catch (QueryException $e) {
            // 5. ¡Deadlock! (Error 1213 o 1205). Espera y reintenta hasta 3 veces.
            if ((str_contains($e->getMessage(), '1213') || str_contains($e->getMessage(), '1205')) && ++$attempts < 3) {
                usleep(random_int(5000, 30000)); // Espera aleatoria (5-30ms)
                goto inicio; // Reintenta
            }
            // Si falla 3 veces o es otro error, lo lanza
            throw $e;
        }
    }

    /**
     * Formatea el número y el año al formato "0001-2025"
     *
     * @param int $numero
     * @param int|null $anio (Opcional)
     * @param int $padding
     * @return string
     */
    public function formatNumber(int $numero, ?int $anio = null, int $padding = 4): string
    {
        $yr = $anio ?? Carbon::now(config('app.timezone'))->year;
        return str_pad($numero, $padding, '0', STR_PAD_LEFT) . '-' . $yr;
    }
}
