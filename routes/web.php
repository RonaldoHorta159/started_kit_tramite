<?php

use App\Http\Controllers\AreaController;
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
});


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
