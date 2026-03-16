<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Headers de segurança aplicados em todas as respostas da API.
     * Protegem contra ataques comuns de navegador e sniffing.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Impede que o navegador adivinhe o tipo do conteúdo
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Impede que a API seja carregada dentro de iframes
        $response->headers->set('X-Frame-Options', 'DENY');

        // Não envia o endereço da API como referrer para outros sites
        $response->headers->set('Referrer-Policy', 'no-referrer');

        // Desativa funcionalidades de navegador que a API não usa
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Remove o header que expõe que o servidor usa PHP
        $response->headers->remove('X-Powered-By');

        // Em produção força HTTPS por 1 ano
        if (app()->environment('production')) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        return $response;
    }
}