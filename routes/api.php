<?php

/**
 * Rotas da API NYXA
 *
 * Organização por grupos de acesso:
 * - Autenticação: rotas públicas com rate limit restritivo
 * - Públicas: rotas de leitura sem autenticação
 * - Protegidas: rotas que exigem token Sanctum
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarController;
use App\Http\Controllers\BarPhotoController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventSubscriptionController;
use App\Http\Controllers\MetricsController;
use App\Http\Controllers\CheckinController;

/**
 * Autenticação via OTP
 * Rate limit restritivo para evitar força bruta
 */
Route::prefix('auth')->middleware('throttle:auth')->group(function () {
    Route::post('/register',   [AuthController::class, 'register']);
    Route::post('/login',      [AuthController::class, 'login']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
});

/**
 * Rotas públicas de eventos e fotos
 * Acessíveis sem autenticação com rate limit generoso
 */
Route::middleware('throttle:api-public')->group(function () {
    Route::get('/events',                    [EventController::class, 'index']);
    Route::get('/events/featured',           [EventController::class, 'featured']);
    Route::get('/events/{event}',            [EventController::class, 'show']);
    Route::get('/bars/{bar}/photos',         [BarPhotoController::class, 'listGalleryPhotos']);
});

/**
 * Rotas protegidas
 * Exigem token Sanctum válido e rate limit por usuário
 */
Route::middleware(['auth:sanctum', 'throttle:api-auth'])->group(function () {

    /** Sessão do usuário autenticado */
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/me',           [AuthController::class, 'me']);

    /** Histórico de inscrições do usuário autenticado */
    Route::get('/me/subscriptions', [EventSubscriptionController::class, 'mySubscriptions']);

    /** Métricas do bar — verificação de permissão feita no controller */
    Route::get('/metrics/bar',            [MetricsController::class, 'barMetrics']);
    Route::get('/metrics/events/{event}', [MetricsController::class, 'eventMetrics']);

    /** Gestão de bars */
    Route::post('/bars',      [BarController::class, 'store']);
    Route::get('/bars/{bar}', [BarController::class, 'show']);
    Route::put('/bars/{bar}', [BarController::class, 'update']);

    /** Fotos do bar */
    Route::post('/bars/{bar}/photo',                    [BarPhotoController::class, 'uploadProfilePhoto']);
    Route::post('/bars/{bar}/photos',                   [BarPhotoController::class, 'addGalleryPhoto']);
    Route::delete('/bars/{bar}/photos/{photo}',         [BarPhotoController::class, 'removeGalleryPhoto']);

    /** Gestão de eventos */
    Route::post('/events',           [EventController::class, 'store']);
    Route::put('/events/{event}',    [EventController::class, 'update']);
    Route::delete('/events/{event}', [EventController::class, 'destroy']);

    /** Inscrições em eventos */
    Route::post('/events/{event}/subscribe',          [EventSubscriptionController::class, 'subscribe']);
    Route::delete('/events/{event}/subscribe',        [EventSubscriptionController::class, 'unsubscribe']);
    Route::get('/events/{event}/subscription-status', [EventSubscriptionController::class, 'checkStatus']);

    /** Check-in via QR Code ou botão no app — feito pelo próprio usuário */
    Route::post('/events/{event}/self-checkin', [CheckinController::class, 'selfCheckin']);
    Route::get('/events/{event}/my-checkin',    [CheckinController::class, 'myCheckin']);

    /** Check-in manual e listagem — apenas bar_owner */
    Route::post('/events/{event}/checkin/{user}', [CheckinController::class, 'checkin']);
    Route::get('/events/{event}/checkins',         [CheckinController::class, 'eventCheckins']);
});