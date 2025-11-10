<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\BandejaEntradaController;
use App\Http\Controllers\Emitir\CorrelativeController;
use App\Http\Controllers\TipoDocumento\TipoDocumentoController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Emitir\EmitirController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

// Agrupamos todas las rutas que requieren autenticación
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/bandeja-entrada', [BandejaEntradaController::class, 'index'])->name('bandeja-entrada.index');
    Route::post('/bandeja-entrada/{documento}/recibir', [BandejaEntradaController::class, 'recibir'])->name('bandeja-entrada.recibir');
    Route::post('/bandeja-entrada/{documento}/derivar', [BandejaEntradaController::class, 'derivar'])->name('bandeja-entrada.derivar');

    // ... (tus rutas de areas, users, tipos-documento) ...
    Route::resource('areas', AreaController::class)->only([
        'index', 'store', 'update', 'destroy'
    ]);

    Route::resource('users', UserController::class)->only([
        'index', 'store', 'update', 'destroy'
    ]);

    Route::resource('tipos-documento', TipoDocumentoController::class)
        ->parameters(['tipos-documento' => 'tipo_documento']) // Opcional: para claridad
        ->only(['index', 'store', 'update', 'destroy']);

    Route::resource('emitir', EmitirController::class)->only([
        'index', 'store', 'update', 'destroy'
    ]);

    // --- INICIO DE LA CORRECCIÓN DE RUTA ---
    Route::get('/correlatives/{tipo_documento}', [CorrelativeController::class, 'show'])
        ->name('correlatives.show')
        ->where('tipo_documento', '[0-9]+')
        ->middleware('throttle:30,1'); // <-- MEJORA DE SEGURIDAD (30 peticiones/min)
    // --- FIN DE LA CORRECCIÓN DE RUTA ---

});


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
