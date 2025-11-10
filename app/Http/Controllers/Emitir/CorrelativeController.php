<?php

namespace App\Http\Controllers\Emitir;

use App\Http\Controllers\Controller;
use App\Models\TipoDocumento; // Usaremos Route-Model binding
use App\Services\CorrelativeService;
use Illuminate\Http\JsonResponse;

class CorrelativeController extends Controller
{
    /**
     * Muestra el siguiente número correlativo disponible para un tipo de documento.
     * Esta ruta es para la consulta asíncrona del frontend[cite: 1650].
     */
    public function show(TipoDocumento $tipo_documento, CorrelativeService $correlativeService): JsonResponse
    {
        // --- INICIO DE LA MEJORA DE AUTORIZACIÓN (Opcional pero recomendado) ---
        // (La dejaremos comentada hasta que creemos la Policy para 'TipoDocumento' o 'Documento')
        // $this->authorize('preview', $tipo_documento);
        // --- FIN DE LA MEJORA ---

        // --- INICIO DE LA MEJORA DE ZONA HORARIA ---
        $anio = now(config('app.timezone'))->year;
        // --- FIN DE LA MEJORA ---

        // 1. Llama al método "peek" (vistazo), que NO bloquea la BD
        $siguienteNumero = $correlativeService->peekNextNumber($tipo_documento->id, $anio);

        // 2. Formatea el número
        $numeroFormateado = $correlativeService->formatNumber($siguienteNumero, $anio);

        // 3. Devuelve una respuesta JSON
        return response()->json([
            'numero' => $siguienteNumero,
            'numero_formateado' => $numeroFormateado,
        ]);
    }
}
