<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventSubscriptionController;
use App\Http\Controllers\MetricsController;

// Rotas de autenticação — rate limit restritivo por IP
Route::prefix('auth')->middleware('throttle:auth')->group(function () {
    Route::post('/register',    [AuthController::class, 'register']);
    Route::post('/login',       [AuthController::class, 'login']);
    Route::post('/verify-otp',  [AuthController::class, 'verifyOtp']);
});

// Rotas públicas de eventos — rate limit generoso
Route::middleware('throttle:api-public')->group(function () {
    Route::get('/events',          [EventController::class, 'index']);
    Route::get('/events/featured', [EventController::class, 'featured']);
    Route::get('/events/{event}',  [EventController::class, 'show']);
});

// Rotas protegidas — exigem token Sanctum + rate limit por usuário
Route::middleware(['auth:sanctum', 'throttle:api-auth'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/me',           [AuthController::class, 'me']);

    // Histórico de inscrições do usuário
    Route::get('/me/subscriptions', [EventSubscriptionController::class, 'mySubscriptions']);

    // Métricas — apenas bar_owner (verificação feita no controller)
    Route::get('/metrics/bar',            [MetricsController::class, 'barMetrics']);
    Route::get('/metrics/events/{event}', [MetricsController::class, 'eventMetrics']);

    // Bars
    Route::post('/bars',      [BarController::class, 'store']);
    Route::get('/bars/{bar}', [BarController::class, 'show']);
    Route::put('/bars/{bar}', [BarController::class, 'update']);

    // Eventos — criação e gestão
    Route::post('/events',           [EventController::class, 'store']);
    Route::put('/events/{event}',    [EventController::class, 'update']);
    Route::delete('/events/{event}', [EventController::class, 'destroy']);

    // Inscrições
    Route::post('/events/{event}/subscribe',            [EventSubscriptionController::class, 'subscribe']);
    Route::delete('/events/{event}/subscribe',          [EventSubscriptionController::class, 'unsubscribe']);
    Route::get('/events/{event}/subscription-status',   [EventSubscriptionController::class, 'checkStatus']);
});