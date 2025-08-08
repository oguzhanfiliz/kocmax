<?php

// Geçici olarak L5Swagger'ı devre dışı bırakmak için
// config/l5-swagger.php dosyasını bu dosya ile değiştirin

return [
    'default' => 'default',
    'documentations' => [],
    'defaults' => [
        'routes' => [
            'docs' => 'api/documentation',
            'oauth2_callback' => 'api/oauth2-callback',
        ],
        'paths' => [
            'use_absolute_path' => env('L5_SWAGGER_USE_ABSOLUTE_PATH', true),
            'docs_json' => 'api-docs.json',
            'docs_yaml' => 'api-docs.yaml',
            'format_to_use_for_docs' => env('L5_FORMAT_TO_USE_FOR_DOCS', 'json'),
            'annotations' => [
                base_path('app'),
            ],
            'excludes' => [],
            'base' => env('L5_SWAGGER_BASE_PATH', null),
            'swagger_ui_assets_path' => env('L5_SWAGGER_UI_ASSETS_PATH', null),
            'excludes' => [],
            'open_api_spec_version' => '3.0.0', // Sabit değer
        ],
    ],
];