<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});


Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('areas', function () {
    return Inertia::render('areas/index');
})->middleware(['auth', 'verified'])->name('areas');

Route::get('tiposDocumentos', function () {
    return Inertia::render('tiposDocumentos/index');
})->middleware(['auth', 'verified'])->name('tiposDocumentos');

Route::get('users', function () {
    return Inertia::render('users/index');
})->middleware(['auth', 'verified'])->name('users');

Route::get('emitir', function () {
    return Inertia::render('emitir/index');
})->middleware(['auth', 'verified'])->name('emitir');

Route::get('bandeja-entrada', function () {
    return Inertia::render('bandeja-entrada/index');
})->middleware(['auth', 'verified'])->name('bandeja-entrada');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
