<?php

namespace App\Http\Controllers;

use App\Enums\EstadoDocumento;
use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        ]);
    }
}
