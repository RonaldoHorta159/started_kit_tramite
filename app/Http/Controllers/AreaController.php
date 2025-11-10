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
        $filters = $request->validate([
            'nombre' => 'nullable|string',
            'codigo' => 'nullable|string',
            'estado' => 'nullable|array',
        ]);

        $sort = $request->validate([
            'sort_field' => 'nullable|string|in:nombre,codigo,estado,created_at',
            'sort_direction' => 'nullable|string|in:asc,desc',
        ]);

        $areas = Area::query()
            ->filterAndSort($filters, $sort)
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return Inertia::render('areas/index', [
            'data' => $areas,
            'filter' => $request->only(['nombre', 'codigo', 'estado']),
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
        Area::create($request->validated());
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

        return redirect()->route('areas.index', $request->query())
            ->with('success', 'Área actualizada correctamente');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Area $area)
    {
        $area->delete();

        return redirect()->route('areas.index', $request->query())
            ->with('success', 'Área eliminada correctamente');
    }
}
