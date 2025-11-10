<?php

namespace App\Http\Controllers\Emitir;

use App\Http\Controllers\Controller;
use App\Http\Requests\Emitir\StoreDocumentoRequest;
use App\Http\Requests\Emitir\UpdateDocumentoRequest;
use App\Models\Documento;
use App\Models\Area;
use App\Models\TipoDocumento;
use App\Models\Movimiento;     // <--- DESCOMENTADO
use App\Services\CorrelativeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // <--- IMPORTADO
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class EmitirController extends Controller
{
    /**
     * Muestra la lista de documentos emitidos por el usuario.
     */
    public function index(Request $request)
    {
        $documentos = Documento::query()
            ->createdBy(Auth::id())
            ->with(['tipoDocumento', 'areaOrigen', 'areaActual'])

            // --- INICIO DE LA CORRECCIÓN DE BÚSQUEDA ---
            ->when($request->input('search'), function ($q, $s) {
                // Se anida la lógica 'orWhere' para no romper el 'createdBy'
                $q->where(function ($qq) use ($s) {
                    $qq->where('codigo_unico', 'like', "%{$s}%")
                        ->orWhere('nro_documento', 'like', "%{$s}%")
                        ->orWhere('asunto', 'like', "%{$s}%");
                });
            })
            // --- FIN DE LA CORRECCIÓN DE BÚSQUEDA ---

            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Datos para los <Select> de los modales
        $areas = Area::where('estado', 'Activo')->select('id', 'nombre')->get();
        $tiposDocumento = TipoDocumento::where('estado', 'Activo')->select('id', 'nombre')->get();

        return Inertia::render('emitir/index', [
            'documentos' => $documentos,
            'filters' => $request->only(['search']),
            'areas' => $areas,
            'tiposDocumento' => $tiposDocumento,
        ]);
    }

    /**
     * Almacena un nuevo documento emitido.
     */
    public function store(StoreDocumentoRequest $request, CorrelativeService $correlativeService)
    {
        $usuario = Auth::user();
        $validatedData = $request->validated();
        // Corrección de zona horaria
        $anio = now(config('app.timezone'))->year;

        $archivoPath = null;
        if ($request->hasFile('archivo')) {
            $archivoPath = $request->file('archivo')->store('documentos', 'private');
        }

        // 1. Genera número (transacción interna del service)
        $siguienteNumero = $correlativeService->generateNextNumber(
            $validatedData['tipo_documento_id'],
            $anio
        );

        $nroDocumento = $correlativeService->formatNumber($siguienteNumero, $anio);
        $codigoUnico = Str::upper(Str::random(10));

        // --- INICIO DE LA CORRECCIÓN DE TRANSACCIÓN ---
        // 2. Envuelve la creación en una transacción para evitar "huecos" [cite: 1601]
        return DB::transaction(function () use ($usuario, $validatedData, $archivoPath, $siguienteNumero, $nroDocumento, $codigoUnico) {

            $documento = Documento::create([
                'user_id' => $usuario->id,
                'area_origen_id' => $usuario->primary_area_id,
                'area_actual_id' => $validatedData['area_destino_id'],
                'tipo_documento_id' => $validatedData['tipo_documento_id'],
                'asunto' => $validatedData['asunto'],
                'folios' => $validatedData['folios'],
                'prioridad' => $validatedData['prioridad'],
                'archivo_path' => $archivoPath,
                'estado' => 'En Trámite',
                'correlativo_tipo' => $siguienteNumero,
                'nro_documento' => $nroDocumento,
                'codigo_unico' => $codigoUnico,
                'correlativo_area' => 0,
                'parent_id' => $validatedData['parent_id'] ?? null,
            ]);

            // 3. Crea el primer movimiento [cite: 1605]
            $documento->movimientos()->create([
                'area_origen_id' => $usuario->primary_area_id,
                'area_destino_id' => $validatedData['area_destino_id'],
                'user_id' => $usuario->id,
                'proveido' => 'Documento de origen',
                'estado' => 'Derivado',
            ]);

            return Redirect::route('emitir.index')->with('success', 'Documento emitido correctamente.');
        });
        // --- FIN DE LA CORRECCIÓN DE TRANSACCIÓN ---
    }

    /**
     * Actualiza un documento emitido.
     */
    public function update(UpdateDocumentoRequest $request, Documento $documento)
    {
        $validatedData = $request->validated();
        $documento->update($validatedData);
        return Redirect::route('emitir.index')->with('success', 'Documento actualizado.');
    }

    /**
     * Elimina un documento emitido.
     */
    public function destroy(Documento $documento)
    {
        if (Auth::id() !== $documento->user_id) {
            return Redirect::route('emitir.index')->with('error', 'No autorizado.');
        }

        if ($documento->archivo_path) {
            Storage::disk('private')->delete($documento->archivo_path);
        }

        $documento->delete();
        return Redirect::route('emitir.index')->with('success', 'Documento eliminado.');
    }
}
