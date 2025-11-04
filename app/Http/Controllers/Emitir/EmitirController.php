<?php

// 1. ESTE ES EL NAMESPACE CORRECTO BASADO EN TU CARPETA
namespace App\Http\Controllers\Emitir;

use App\Http\Controllers\Controller; // <--- Importante
use App\Http\Requests\Emitir\StoreDocumentoRequest;
use App\Http\Requests\Emitir\UpdateDocumentoRequest;
use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Models\Area;
use App\Models\TipoDocumento;

// 2. Extiende de 'Controller', no de 'EmitirController'
class EmitirController extends Controller
{
    /**
     * Muestra la lista de documentos emitidos por el usuario.
     */
    /**
     * Muestra la lista de documentos emitidos por el usuario.
     */
    public function index(Request $request)
    {
        $documentos = Documento::query()
            ->createdBy(Auth::id())
            ->with(['tipoDocumento', 'areaOrigen', 'areaActual'])
            ->when($request->input('search'), function ($query, $search) {
                $query->where('codigo_unico', 'like', "%{$search}%")
                    ->orWhere('nro_documento', 'like', "%{$search}%")
                    ->orWhere('asunto', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // 2. Obtenemos los datos para los <Select> de los modales
        // (Esto es crucial para CreateDialog y EditDialog)
        $areas = Area::where('estado', 'Activo')->select('id', 'nombre')->get();
        $tiposDocumento = TipoDocumento::where('estado', 'Activo')->select('id', 'nombre')->get();

        // 3. Pasamos los nuevos datos a la vista
        return Inertia::render('emitir/index', [
            'documentos' => $documentos,
            'filters' => $request->only(['search']),
            'areas' => $areas,                 // <-- AÑADIDO
            'tiposDocumento' => $tiposDocumento, // <-- AÑADIDO
        ]);
    }

    /**
     * Almacena un nuevo documento emitido.
     */
    public function store(StoreDocumentoRequest $request)
    {
        $usuario = Auth::user();
        $validatedData = $request->validated();

        $archivoPath = null;
        if ($request->hasFile('archivo')) {
            $archivoPath = $request->file('archivo')->store('documentos', 'private');
        }

        // TODO: Mover a CorrelativeService
        $temporalCorrelativo = Documento::where('tipo_documento_id', $validatedData['tipo_documento_id'])
            ->where('anio_creacion', now()->year)->count() + 1;
        $nroDocumento = str_pad($temporalCorrelativo, 4, '0', STR_PAD_LEFT) . '-' . now()->year;
        $codigoUnico = Str::random(10); // Placeholder

        $documento = Documento::create([
            'user_id' => $usuario->id,
            'area_origen_id' => $usuario->primary_area_id,
            'area_actual_id' => $validatedData['area_destino_id'],
            'tipo_documento_id' => $validatedData['tipo_documento_id'],
            'asunto' => $validatedData['asunto'],
            'folios' => $validatedData['folios'],
            'prioridad' => $validatedData['prioridad'] ?? 'Normal',
            'archivo_path' => $archivoPath,
            'estado' => 'En Trámite',
            'correlativo_tipo' => $temporalCorrelativo,
            'nro_documento' => $nroDocumento,
            'codigo_unico' => $codigoUnico,
            'correlativo_area' => 0,
            'parent_id' => $validatedData['parent_id'] ?? null,
        ]);

        return Redirect::route('emitir.index')->with('success', 'Documento emitido correctamente.');
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
