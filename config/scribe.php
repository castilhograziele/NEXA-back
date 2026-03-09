<?php

use Knuckles\Scribe\Config\AuthIn;
use Knuckles\Scribe\Config\Defaults;
use Knuckles\Scribe\Extracting\Strategies;

use function Knuckles\Scribe\Config\configureStrategy;

return [
    'title' => 'barespf API Documentation',

    'description' => 'API para a plataforma barespf — descubra eventos em bares da sua cidade.',

    'intro_text' => <<<'INTRO'
        Esta documentação descreve todos os endpoints disponíveis na API do barespf.

        <aside>Use o botão <b>Try It Out</b> para testar os endpoints diretamente aqui na documentação.</aside>
    INTRO,

    'base_url' => config('app.url'),

    'routes' => [
        [
            'match' => [
                'prefixes' => ['api/*'],
                'domains'  => ['*'],
            ],
            'include' => [],
            'exclude' => [],
        ],
    ],

    'type'  => 'laravel',
    'theme' => 'default',

    'static' => [
        'output_path' => 'public/docs',
    ],

    'laravel' => [
        'add_routes'       => true,
        'docs_url'         => '/docs',
        'assets_directory' => null,
        'middleware'       => [],
    ],

    'external' => [
        'html_attributes' => [],
    ],

    'try_it_out' => [
        'enabled'  => true,
        'base_url' => null,
        'use_csrf' => false,
        'csrf_url' => '/sanctum/csrf-cookie',
    ],

    // Configuração de autenticação via Bearer Token (Sanctum)
    'auth' => [
        'enabled'     => true,
        'default'     => false, // por padrão os endpoints são públicos
        'in'          => AuthIn::BEARER->value,
        'name'        => 'Authorization',
        'use_value'   => env('SCRIBE_AUTH_KEY'),
        'placeholder' => '{SEU_TOKEN_AQUI}',
        'extra_info'  => 'Faça login em <b>POST /api/auth/login</b> para obter seu token.',
    ],

    // Exemplos em bash e javascript
    'example_languages' => [
        'bash',
        'javascript',
    ],

    'postman' => [
        'enabled'   => true,
        'overrides' => [],
    ],

    'openapi' => [
        'enabled'    => true,
        'version'    => '3.0.3',
        'overrides'  => [],
        'generators' => [],
    ],

    'groups' => [
        'default' => 'Endpoints',
        'order'   => [
            'Autenticação',
            'Bars',
            'Events',
            'Inscrições',
            'Métricas',
        ],
    ],

    'logo'         => false,
    'last_updated' => 'Atualizado em: {date:d/m/Y}',

    'examples' => [
        'faker_seed'    => 1234,
        'models_source' => ['factoryCreate', 'factoryMake', 'databaseFirst'],
    ],

    'strategies' => [
        'metadata' => [
            ...Defaults::METADATA_STRATEGIES,
        ],
        'headers' => [
            ...Defaults::HEADERS_STRATEGIES,
            Strategies\StaticData::withSettings(data: [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ]),
        ],
        'urlParameters'  => [...Defaults::URL_PARAMETERS_STRATEGIES],
        'queryParameters' => [...Defaults::QUERY_PARAMETERS_STRATEGIES],
        'bodyParameters' => [...Defaults::BODY_PARAMETERS_STRATEGIES],
        'responses' => configureStrategy(
            Defaults::RESPONSES_STRATEGIES,
            Strategies\Responses\ResponseCalls::withSettings(
                only: ['GET *'],
                config: ['app.debug' => false]
            )
        ),
        'responseFields' => [...Defaults::RESPONSE_FIELDS_STRATEGIES],
    ],

    'database_connections_to_transact' => [config('database.default')],

    'fractal' => [
        'serializer' => null,
    ],
];