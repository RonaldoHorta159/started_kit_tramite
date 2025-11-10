<?php

namespace App\Http\Controllers\TipoDocumento;

use App\Http\Controllers\Controller;
use App\Http\Requests\TipoDocumentos\StoreTipoDocumentoRequest;
use App\Http\Requests\TipoDocumentos\UpdateTipoDocumentoRequest;
use App\Models\TipoDocumento;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TipoDocumentoController extends Controller
{
    /**
     * Listado con búsqueda, filtros y orden.
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => 'nullable|string',
            'estado' => 'nullable|array',
        ]);

        $sort = $request->validate([
            'sort_field' => 'nullable|string|in:nombre,estado,created_at',
            'sort_direction' => 'nullable|string|in:asc,desc',
        ]);

        $tipos = TipoDocumento::query()
            ->filterAndSort($filters, $sort)
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return Inertia::render('tipos-documento/index', [
            'data' => $tipos,
            'filter' => $filters,
            'estadosOptions' => ['Activo', 'Inactivo'],
        ]);
    }

    public function create()
    { /* no usado con Inertia */
    }

    /**
     * Crear.
     */
    public function store(StoreTipoDocumentoRequest $request)
    {
        $data = $request->validated();
        TipoDocumento::create($data);

        return back()->with('success', 'Tipo de documento creado correctamente.');
    }

    public function show(string $id)
    { /* no usado */
    }

    public function edit(string $id)
    { /* no usado */
    }

    /**
     * Actualizar.
     */
    public function update(UpdateTipoDocumentoRequest $request, TipoDocumento $tipos_documento)
    {
        $data = $request->validated();
        $tipos_documento->update($data);

        return back()->with('success', 'Tipo de documento actualizado correctamente.');
    }

    /**
     * Eliminar.
     */
    public function destroy(Request $request, TipoDocumento $tipos_documento)
    {
        $tipos_documento->delete();

        // Mantén los query params al volver al listado
        return redirect()->route('tipos-documento.index', $request->query())
            ->with('success', 'Tipo de documento eliminado correctamente.');
    }
}
