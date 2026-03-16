<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use App\Models\PersonalAccessToken;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        $this->configureRateLimiting();
    }

    /**
     * Define os limites de requisição por endpoint.
     * Cada limite é configurado via .env para fácil ajuste por ambiente.
     */
    private function configureRateLimiting(): void
    {
        // OTP — máximo 5 tentativas por IP em 1 minuto
        // Protege contra força bruta no envio e verificação de código
        RateLimiter::for('otp', function (Request $request) {
            return Limit::perMinute(
                (int) env('RATE_LIMIT_OTP', 5)
            )->by($request->ip());
        });

        // Auth geral (login/register) — 10 por minuto por IP
        // Mais generoso que OTP pois não envolve código secreto
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(
                (int) env('RATE_LIMIT_AUTH', 10)
            )->by($request->ip());
        });

        // API pública — 60 por minuto por IP
        // Cobre listagem de eventos e outros endpoints públicos
        RateLimiter::for('api-public', function (Request $request) {
            return Limit::perMinute(
                (int) env('RATE_LIMIT_API_PUBLIC', 60)
            )->by($request->ip());
        });

        // API autenticada — 120 por minuto por usuário
        // Limite por user_id evita bypass via rotação de IP
        RateLimiter::for('api-auth', function (Request $request) {
            return Limit::perMinute(
                (int) env('RATE_LIMIT_API_AUTH', 120)
            )->by($request->user()?->id ?? $request->ip());
        });
    }
}