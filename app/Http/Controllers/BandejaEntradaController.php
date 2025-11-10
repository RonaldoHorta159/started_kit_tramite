<?php

namespace App\Http\Controllers;

use App\Enums\EstadoDocumento;
use App\Enums\EstadoMovimiento;
use App\Http\Requests\Bandeja\DerivarDocumentoRequest;
use App\Models\Area;
use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BandejaEntradaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->primary_area_id) {
            // Opcional: manejar el caso de que un usuario no tenga área principal
            return Inertia::render('bandeja-entrada/index', [
                'data' => [],
                'filter' => [],
                'estadosOptions' => [],
                'areasOptions' => [],
            ]);
        }

        $filters = $request->validate([
            'q' => 'nullable|string',
            'estado' => 'nullable|array',
        ]);

        $sort = $request->validate([
            'sort_field' => 'nullable|string|in:nro_documento,asunto,estado,created_at',
            'sort_direction' => 'nullable|string|in:asc,desc',
        ]);

        $documentos = Documento::query()
            ->with(['tipoDocumento:id,nombre', 'areaOrigen:id,nombre'])
            ->forInbox($user->primary_area_id)
            ->when($filters['q'] ?? null, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nro_documento', 'like', '%'.$search.'%')
                        ->orWhere('asunto', 'like', '%'.$search.'%');
                });
            })
            ->when($filters['estado'] ?? null, function ($q, $estado) {
                $q->whereIn('estado', (array) $estado);
            })
            ->orderBy($sort['field'] ?? 'created_at', $sort['direction'] ?? 'desc')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return Inertia::render('bandeja-entrada/index', [
            'data' => $documentos,
            'filter' => $filters,
            'estadosOptions' => collect(EstadoDocumento::cases())->map(fn ($e) => $e->value)->all(),
            'areasOptions' => Area::where('estado', 'Activo')->orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function recibir(Documento $documento)
    {
        $user = Auth::user();

        // Autorización: solo el área actual puede recibir
        if ($documento->area_actual_id !== $user->primary_area_id) {
            abort(403, 'No autorizado para esta acción.');
        }

        // Solo se puede recibir si está 'En Trámite'
        if ($documento->estado !== EstadoDocumento::EN_TRAMITE) {
            return back()->with('error', 'Este documento no se puede recibir.');
        }

        DB::transaction(function () use ($documento, $user) {
            // 1. Actualizar estado del documento
            $documento->update(['estado' => EstadoDocumento::RECIBIDO]);

            // 2. Crear el movimiento de recepción
            $documento->movimientos()->create([
                'area_origen_id' => $documento->area_actual_id, // El origen es el área actual
                'area_destino_id' => $documento->area_actual_id, // El destino es la misma área
                'user_id' => $user->id,
                'proveido' => 'Documento recibido por el área.',
                'estado' => EstadoMovimiento::RECIBIDO,
            ]);
        });

        return back()->with('success', 'Documento recibido correctamente.');
    }

    public function derivar(DerivarDocumentoRequest $request, Documento $documento)
    {
        $user = Auth::user();
        $validated = $request->validated();

        // Autorización: solo el área actual puede derivar
        if ($documento->area_actual_id !== $user->primary_area_id) {
            abort(403, 'No autorizado para esta acción.');
        }

        // No se puede derivar si no está Recibido
        if ($documento->estado !== EstadoDocumento::RECIBIDO) {
            return back()->with('error', 'Solo se pueden derivar documentos recibidos.');
        }

        DB::transaction(function () use ($documento, $user, $validated) {
            // 1. Actualizar el documento
            $documento->update([
                'area_actual_id' => $validated['area_destino_id'],
                'estado' => EstadoDocumento::EN_TRAMITE, // Vuelve a estar 'En Trámite' para la nueva área
            ]);

            // 2. Crear el movimiento de derivación
            $documento->movimientos()->create([
                'area_origen_id' => $user->primary_area_id,
                'area_destino_id' => $validated['area_destino_id'],
                'user_id' => $user->id,
                'proveido' => $validated['proveido'],
                'estado' => EstadoMovimiento::DERIVADO,
            ]);
        });

        return back()->with('success', 'Documento derivado correctamente.');
    }
}
