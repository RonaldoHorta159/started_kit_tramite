<?php

use App\Http\Controllers\AreaController;
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

    // ... (tus rutas de areas, users, tipos-documento) ...
    Route::resource('areas', AreaController::class)->only([
        'index', 'store', 'update', 'destroy'
    ]);

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::resource('tipos-documento', TipoDocumentoController::class)
        ->parameters(['tipos-documento' => 'tipo_documento']) // Opcional: para claridad
        ->only(['index', 'store', 'update', 'destroy']);

    Route::prefix('emitir')->name('emitir.')->group(function () {
        Route::get('/', [EmitirController::class, 'index'])->name('index');
        Route::post('/', [EmitirController::class, 'store'])->name('store');
        Route::put('/{documento}', [EmitirController::class, 'update'])->name('update');
        Route::delete('/{documento}', [EmitirController::class, 'destroy'])->name('destroy');
    });

    // --- INICIO DE LA CORRECCIÓN DE RUTA ---
    Route::get('/correlatives/{tipo_documento}', [CorrelativeController::class, 'show'])
        ->name('correlatives.show')
        ->where('tipo_documento', '[0-9]+')
        ->middleware('throttle:30,1'); // <-- MEJORA DE SEGURIDAD (30 peticiones/min)
    // --- FIN DE LA CORRECCIÓN DE RUTA ---

});


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
