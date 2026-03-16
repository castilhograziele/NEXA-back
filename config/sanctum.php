<?php

use Laravel\Sanctum\Sanctum;

return [

    /*
     * Domínios que recebem autenticação stateful via cookie.
     * Para a API NEXA usamos apenas Bearer Token,
     * mas mantemos os domínios locais configurados.
     */
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
        Sanctum::currentApplicationUrlWithPort(),
    ))),

    'guard' => ['web'],

    /*
     * Tempo de expiração dos tokens em minutos.
     * 480 = 8 horas — padrão para usuários comuns.
     * Configurável via .env para ajustar por ambiente.
     * null = nunca expira (inseguro para produção).
     */
    'expiration' => (int) env('SANCTUM_TOKEN_EXPIRATION', 480),

    /*
     * Prefixo dos tokens — permite que plataformas de segurança
     * detectem e alertem sobre tokens commitados em repositórios.
     */
    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', 'nyxa_'),

    'middleware' => [
        'authenticate_session'       => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies'            => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token'        => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
        'personal_access_token_model' => \App\Models\PersonalAccessToken::class,
    ],

];