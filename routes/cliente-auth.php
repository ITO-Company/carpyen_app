<?php

use App\Http\Controllers\ClienteAuth\ClienteAuthController;
use App\Http\Controllers\ClienteAuth\ClientePortalController;
use Illuminate\Support\Facades\Route;

// Guest routes for clients
Route::middleware('guest:cliente')->prefix('cliente')->group(function () {
    Route::get('login', [ClienteAuthController::class, 'create'])
        ->name('cliente.login');

    Route::post('login', [ClienteAuthController::class, 'store']);
});

// Authenticated routes for clients
Route::middleware('auth:cliente')->prefix('cliente')->group(function () {
    Route::get('dashboard', [ClientePortalController::class, 'dashboard'])
        ->name('cliente.dashboard');

    Route::get('proyectos/{proyectoId}', [ClientePortalController::class, 'showProyecto'])
        ->name('cliente.proyectos.show');

    Route::get('planes/{planPagoId}', [ClientePortalController::class, 'showPlanPago'])
        ->name('cliente.planes.show');

    Route::post('logout', [ClienteAuthController::class, 'destroy'])
        ->name('cliente.logout');
});
