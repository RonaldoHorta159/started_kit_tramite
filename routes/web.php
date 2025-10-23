<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\TipoDocumento\TipoDocumentoController;
use App\Http\Controllers\User\UserController;
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

    // Route::resource('/areas', AreaController::class);
    Route::prefix('areas')->group(function () {
        Route::get('/', [AreaController::class, 'index'])->name('areas.index');
        Route::post('/', [AreaController::class, 'store'])->name('areas.store');
        Route::put('/{area}', [AreaController::class, 'update'])->name('areas.update');
        Route::delete('/{area}', [AreaController::class, 'destroy'])->name('areas.destroy');
    });

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::prefix('tipos-documento')->name('tipos-documento.')->group(function () {
        Route::get('/', [TipoDocumentoController::class, 'index'])->name('index');
        Route::post('/', [TipoDocumentoController::class, 'store'])->name('store');
        Route::put('/{tipos_documento}', [TipoDocumentoController::class, 'update'])->name('update');
        Route::delete('/{tipos_documento}', [TipoDocumentoController::class, 'destroy'])->name('destroy');
    });


});


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
