<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Models\Area;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            : null;

        $rolVals = $request->has('rol')
            ? array_values(array_filter((array) $request->input('rol'), fn($v) => $v !== ''))
            : null;

        // Área filtrada: soporta null y evita '*' (del front) o valores no numéricos
        $areaParam = $request->input('area_id');
        $areaId = is_numeric($areaParam) ? (int) $areaParam : null;

        $q = User::query()
            ->with([
                'primaryArea:id,nombre',       // área principal
                'areas:id,nombre',             // áreas adicionales (pivot)
            ])
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
            // Área: principal O adicional (pivot area_user)
            ->when($areaId, function ($qb) use ($areaId) {
                $qb->where(function ($q2) use ($areaId) {
                    $q2->where('primary_area_id', $areaId)
                        ->orWhereExists(function ($s) use ($areaId) {
                            $s->from('area_user')
                                ->whereColumn('area_user.user_id', 'users.id')
                                ->where('area_user.area_id', $areaId);
                        });
                });
            })
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
        $areasOptions = Area::orderBy('nombre')->get(['id', 'nombre']);
        $rolesOptions = ['Admin', 'Usuario', 'Mesa de Partes'];
        $estadosOptions = ['Activo', 'Inactivo'];

        return Inertia::render('users/index', [
            'data' => $users,           // paginator
            'filter' => $filters,       // [{id,value}, ...]
            'areasOptions' => $areasOptions,
            'rolesOptions' => $rolesOptions,
            'estadosOptions' => $estadosOptions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $areas = $request->input('areas_ids', []); // puede venir vacío

        DB::transaction(function () use ($data, $areas) {
            $data['password'] = Hash::make($data['password']);
            /** @var \App\Models\User $user */
            $user = User::create($data);
            // Pivot N:M (áreas adicionales)
            $user->areas()->sync($areas);
        });

        return back()->with('success', 'Usuario creado correctamente.');
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

        DB::transaction(function () use ($user, $data, $request) {
            $user->update($data);

            // Solo sincroniza pivot si el payload incluye 'areas_ids'.
            // Esto permite "dejar igual" cuando el front no lo envía.
            if ($request->has('areas_ids')) {
                $user->areas()->sync($request->input('areas_ids', []));
            }
        });

        return back()->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $user)
    {
        $user->delete();

        // Vuelve a la lista con los mismos query params
        return redirect()->route('users.index', $request->query())
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
