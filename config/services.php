<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | Merge these entries into your application's existing config/services.php.
    |
    */

    'azure_openai' => [
        'endpoint'    => env('AZURE_OPENAI_ENDPOINT'),
        'key'         => env('AZURE_OPENAI_KEY'),
        'deployment'  => env('AZURE_OPENAI_DEPLOYMENT'),
        'api_version' => env('AZURE_OPENAI_API_VERSION', '2024-08-01-preview'),
    ],

];
