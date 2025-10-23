<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = max(1, min(100, (int) $request->integer('per_page', 10)));
        $page = (int) $request->integer('page', 1);

        // Orden seguro
        $allowedSorts = ['dni', 'nombres', 'email', 'rol', 'estado', 'created_at'];
        $sortField = in_array($request->get('sort_field'), $allowedSorts, true)
            ? $request->get('sort_field') : 'nombres';
        $sortDir = $request->get('sort_direction') === 'desc' ? 'desc' : 'asc';

        // Filtros multi
        $estadoVals = $request->has('estado')
            ? array_values(array_filter((array) $request->input('estado'), fn($v) => $v !== ''))
            : null; // ['Activo','Inactivo']

        $rolVals = $request->has('rol')
            ? array_values(array_filter((array) $request->input('rol'), fn($v) => $v !== ''))
            : null; // ['Admin','Usuario','Mesa de Partes']

        $areaId = $request->integer('area_id'); // área primaria

        $q = \App\Models\User::query()
            ->with(['primaryArea']) // necesitas el método primaryArea() en el modelo
            // Búsqueda de texto (dni, nombres, apellidos, email)
            ->when($request->filled('q'), function ($qb) use ($request) {
                $v = trim((string) $request->string('q'));
                $qb->where(function ($sub) use ($v) {
                    $sub->where('dni', 'like', "%{$v}%")
                        ->orWhere('email', 'like', "%{$v}%")
                        ->orWhere('nombres', 'like', "%{$v}%")
                        ->orWhere('apellido_paterno', 'like', "%{$v}%")
                        ->orWhere('apellido_materno', 'like', "%{$v}%");
                });
            })
            // Estado (Activo/Inactivo)
            ->when($estadoVals && count($estadoVals) > 0, fn($qb) => $qb->whereIn('estado', $estadoVals))
            // Rol (Admin/Usuario/Mesa de Partes)
            ->when($rolVals && count($rolVals) > 0, fn($qb) => $qb->whereIn('rol', $rolVals))
            // Área primaria
            ->when($areaId, fn($qb, $aid) => $qb->where('primary_area_id', $aid))
            ->orderBy($sortField, $sortDir);

        $users = $q->paginate($perPage, ['*'], 'page', $page)
            ->appends($request->query());

        // Rehidratación de filtros para el front (TanStack columnFilters)
        $filters = collect([
            'q' => $request->input('q'),
            'estado' => $estadoVals ?: null,
            'rol' => $rolVals ?: null,
            'area_id' => $areaId ?: null,
        ])->filter(fn($v) => $v !== null && $v !== '' && $v !== [])
            ->map(fn($v, $k) => ['id' => $k, 'value' => $v])
            ->values()->all();

        // Opciones para selects (áreas, roles, estados)
        $areasOptions = \App\Models\Area::orderBy('nombre')->get(['id', 'nombre']);
        $rolesOptions = ['Admin', 'Usuario', 'Mesa de Partes'];
        $estadosOptions = ['Activo', 'Inactivo'];

        return Inertia::render('users/index', [
            'data' => $users,         // paginator (data, current_page, per_page, last_page, ...)
            'filter' => $filters,       // [{id,value}, ...] para hidratar filtros
            'areasOptions' => $areasOptions,
            'rolesOptions' => $rolesOptions,
            'estadosOptions' => $estadosOptions,
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
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return back()->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        if (empty($data['password'])) {
            unset($data['password']); // no cambiar si viene vacío
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return back()->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, \App\Models\User $user)
    {
        $user->delete();

        // Igual que en áreas: vuelve a la lista con los mismos query params
        return redirect()->route('users.index', $request->query())
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
