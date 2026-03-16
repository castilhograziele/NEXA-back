<?php

use App\Http\Middleware\SecurityHeadersMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // CORS — deve ser o primeiro da fila
        $middleware->prepend(HandleCors::class);

        // Headers de segurança em todas as respostas
        $middleware->append(SecurityHeadersMiddleware::class);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();