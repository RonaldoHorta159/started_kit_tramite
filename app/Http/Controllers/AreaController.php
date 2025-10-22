<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAreaRequest;
use App\Http\Requests\UpdateAreaRequest;
use App\Models\Area;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = max(1, min(100, (int) $request->integer('per_page', 10)));
        $page = (int) $request->integer('page', 1);

        $allowedSorts = ['nombre', 'codigo', 'estado', 'created_at'];
        $sortField = in_array($request->get('sort_field'), $allowedSorts, true)
            ? $request->get('sort_field') : 'nombre';
        $sortDir = $request->get('sort_direction') === 'desc' ? 'desc' : 'asc';

        $estadoVals = $request->has('estado')
            ? array_values(array_filter((array) $request->input('estado'), fn($v) => $v !== ''))
            : null;

        $q = Area::query()
            ->when($request->filled('nombre'), function ($q) use ($request) {
                $v = trim((string) $request->input('nombre'));   // <- OJO: nombre
                $q->where('nombre', 'like', "%{$v}%");
            })
            ->when($request->filled('codigo'), function ($q) use ($request) {
                $v = trim((string) $request->input('codigo'));
                $q->where('codigo', 'like', "%{$v}%");
            })
            ->when($estadoVals && count($estadoVals) > 0, fn($q) => $q->whereIn('estado', $estadoVals))
            ->orderBy($sortField, $sortDir);

        $areas = $q->paginate($perPage, ['*'], 'page', $page)->appends($request->query());

        $filters = collect([
            'nombre' => $request->input('nombre'),
            'codigo' => $request->input('codigo'),
            'estado' => $estadoVals ?: null,
        ])->filter(fn($v) => $v !== null && $v !== '' && $v !== [])
            ->map(fn($v, $k) => ['id' => $k, 'value' => $v])
            ->values()->all();

        return Inertia::render('areas/index', [
            'data' => $areas,
            'filter' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAreaRequest $request)
    {
        Area::create([
            'nombre' => $request->nombreArea,
            'codigo' => $request->codigo ?? null,
            'estado' => $request->estadoArea,
        ]);
        return to_route('areas.index')->with('success', 'Área creada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Area $area)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Area $area)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAreaRequest $request, Area $area)
    {
        $area->update($request->validated());
        return to_route('areas.index')->with('success', 'Area actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Area $area)
    {
        $area->delete();
        return to_route('areas.index')->with('success', 'Area eliminada correctamente');
    }
}
