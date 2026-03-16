<?php

return [

    /*
     * Rotas que aceitam requisições CORS.
     * O padrão 'api/*' cobre todos os endpoints da API.
     */
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    /*
     * Origens permitidas vêm do .env para facilitar
     * troca entre ambientes sem mudar código.
     */
    'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:3000')),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    /*
     * Em produção com Sanctum, credentials precisa ser true
     * para o frontend enviar o Bearer Token corretamente.
     */
    'supports_credentials' => true,

];