<?php

use App\Http\Controllers\AreaController; // 👈 1. Importar el nuevo controlador
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

    // 2. Reemplazamos la ruta GET por una ruta de RECURSO para Áreas
    Route::resource('areas', AreaController::class);

    Route::get('tiposDocumentos', function () {
        return Inertia::render('tiposDocumentos/index');
    })->name('tiposDocumentos');

    Route::get('users', function () {
        return Inertia::render('users/index');
    })->name('users');

    Route::get('emitir', function () {
        return Inertia::render('emitir/index');
    })->name('emitir');

    Route::get('bandeja-entrada', function () {
        return Inertia::render('bandeja-entrada/index');
    })->name('bandeja-entrada');
});


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
