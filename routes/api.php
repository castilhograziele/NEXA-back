<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventSubscriptionController;
use App\Http\Controllers\MetricsController;

// Rotas públicas de autenticação
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
});

// Rotas públicas de eventos — qualquer visitante pode ver
Route::get('/events',         [EventController::class, 'index']);
Route::get('/events/{event}', [EventController::class, 'show']);

// Rotas protegidas — exigem token Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/me',           [AuthController::class, 'me']);

    // Histórico de inscrições do usuário
    Route::get('/me/subscriptions', [EventSubscriptionController::class, 'mySubscriptions']);

    // Rotas de bars
    Route::post('/bars',      [BarController::class, 'store']);
    Route::get('/bars/{bar}', [BarController::class, 'show']);
    Route::put('/bars/{bar}', [BarController::class, 'update']);

    // Rotas de events — criação e gestão
    Route::post('/events',           [EventController::class, 'store']);
    Route::put('/events/{event}',    [EventController::class, 'update']);
    Route::delete('/events/{event}', [EventController::class, 'destroy']);

    // Inscrições em eventos
    Route::post('/events/{event}/subscribe',   [EventSubscriptionController::class, 'subscribe']);
    Route::delete('/events/{event}/subscribe', [EventSubscriptionController::class, 'unsubscribe']);

    // Métricas — apenas bar_owner
    Route::get('/metrics/bar',              [MetricsController::class, 'barMetrics']);
    Route::get('/metrics/events/{event}',   [MetricsController::class, 'eventMetrics']);
});