<?php

namespace App\Http\Controllers\User;

use App\Enums\Estado;
use App\Enums\Rol;
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
        $filters = $request->validate([
            'q' => 'nullable|string',
            'estado' => 'nullable|array',
            'rol' => 'nullable|array',
            'area_id' => 'nullable|integer',
        ]);

        $sort = $request->validate([
            'sort_field' => 'nullable|string|in:dni,nombres,email,rol,estado,created_at',
            'sort_direction' => 'nullable|string|in:asc,desc',
        ]);

        $users = User::query()
            ->with('primaryArea:id,nombre')
            ->filterAndSort($filters, $sort)
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return Inertia::render('users/index', [
            'data' => $users,
            'filter' => $filters,
            'areasOptions' => Area::orderBy('nombre')->get(['id', 'nombre']),
            'rolesOptions' => collect(Rol::cases())->map(fn ($r) => $r->value)->all(),
            'estadosOptions' => collect(Estado::cases())->map(fn ($e) => $e->value)->all(),
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
