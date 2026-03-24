<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Route::post('/login', [AuthController::class, 'login']);

// ─── Carry-Forward API (auto-populate from prior month) ─────────
Route::middleware('auth:sanctum')->prefix('carry-forward')->group(function () {
    Route::get('/harvest', [\App\Http\Controllers\Api\CarryForwardController::class, 'harvest']);
    Route::get('/pollen', [\App\Http\Controllers\Api\CarryForwardController::class, 'pollen']);
});
