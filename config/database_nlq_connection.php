<?php

/*
|--------------------------------------------------------------------------
| NLQ read-only connection
|--------------------------------------------------------------------------
|
| Merge this 'nlq_readonly' entry into the 'connections' array of your
| application's config/database.php file. It is kept in a separate file
| here to avoid overwriting your existing database configuration.
|
*/

return [
    'nlq_readonly' => [
        'driver'   => 'pgsql',
        'host'     => env('NLQ_DB_HOST', '127.0.0.1'),
        'port'     => env('NLQ_DB_PORT', '5432'),
        'database' => env('NLQ_DB_DATABASE'),
        'username' => env('NLQ_DB_USERNAME'),
        'password' => env('NLQ_DB_PASSWORD'),
        'charset'  => 'utf8',
        'schema'   => 'public',
        'sslmode'  => 'prefer',
    ],
];
