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
        $perPage = max(1, min(100, (int) $request->integer('per_page', 10)));
        $page = (int) $request->integer('page', 1);

        // Orden seguro
        $allowedSorts = ['nombre', 'estado', 'created_at'];
        $sortField = in_array($request->get('sort_field'), $allowedSorts, true)
            ? $request->get('sort_field') : 'nombre';
        $sortDir = $request->get('sort_direction') === 'desc' ? 'desc' : 'asc';

        // Filtro múltiple por estado
        $estadoVals = $request->has('estado')
            ? array_values(array_filter((array) $request->input('estado'), fn($v) => $v !== ''))
            : null; // ['Activo','Inactivo']

        $query = TipoDocumento::query()
            // Búsqueda por nombre
            ->when($request->filled('q'), function ($qb) use ($request) {
                $v = trim((string) $request->string('q'));
                $qb->where('nombre', 'like', "%{$v}%");
            })
            // Filtro estado
            ->when($estadoVals && count($estadoVals) > 0, fn($qb) => $qb->whereIn('estado', $estadoVals))
            ->orderBy($sortField, $sortDir);

        $tipos = $query->paginate($perPage, ['*'], 'page', $page)
            ->appends($request->query());

        // Rehidratación de filtros para TanStack (array de {id,value})
        $filters = collect([
            'q' => $request->input('q'),
            'estado' => $estadoVals ?: null,
        ])->filter(fn($v) => $v !== null && $v !== '' && $v !== [])
            ->map(fn($v, $k) => ['id' => $k, 'value' => $v])
            ->values()
            ->all();

        $estadosOptions = ['Activo', 'Inactivo'];

        return Inertia::render('tipos-documento/index', [
            'data' => $tipos,          // paginator
            'filter' => $filters,        // [{id,value}, ...]
            'estadosOptions' => $estadosOptions,
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
