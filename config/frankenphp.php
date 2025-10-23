<?php

return [
    /*
    |--------------------------------------------------------------------------
    | FrankenPHP Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for FrankenPHP, a modern PHP
    | application server. FrankenPHP provides high-performance execution
    | with worker mode support for improved performance.
    |
    */

    'worker' => [
        /*
        |--------------------------------------------------------------------------
        | Maximum Requests Per Worker
        |--------------------------------------------------------------------------
        |
        | The maximum number of requests a worker process will handle before
        | being restarted. This helps prevent memory leaks and ensures
        | fresh state for each batch of requests.
        |
        */
        'max_requests' => env('FRANKENPHP_MAX_REQUESTS', 1000),

        /*
        |--------------------------------------------------------------------------
        | Memory Limit
        |--------------------------------------------------------------------------
        |
        | The maximum amount of memory a worker process can use before
        | being restarted. This is specified as a string with units
        | (e.g., '128M', '512M', '1G').
        |
        */
        'memory_limit' => env('FRANKENPHP_MEMORY_LIMIT', '512M'),

        /*
        |--------------------------------------------------------------------------
        | Persistent Connections
        |--------------------------------------------------------------------------
        |
        | Enable persistent connections for Redis and database connections.
        | This improves performance by reusing connections across requests
        | in worker mode.
        |
        */
        'persistent_connections' => [
            'redis' => env('FRANKENPHP_PERSISTENT_REDIS', true),
            'database' => env('FRANKENPHP_PERSISTENT_DB', true),
        ],

        /*
        |--------------------------------------------------------------------------
        | Connection Pool Settings
        |--------------------------------------------------------------------------
        |
        | Configuration for connection pooling in worker mode. These settings
        | help optimize resource usage and connection management.
        |
        */
        'connection_pool' => [
            'min_connections' => env('FRANKENPHP_POOL_MIN', 1),
            'max_connections' => env('FRANKENPHP_POOL_MAX', 10),
            'connection_ttl' => env('FRANKENPHP_CONNECTION_TTL', 3600),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Metrics and Monitoring
    |--------------------------------------------------------------------------
    |
    | Enable metrics collection for monitoring worker performance and
    | resource usage in production environments.
    |
    */
    'metrics' => [
        'enabled' => env('FRANKENPHP_METRICS_ENABLED', true),
        'endpoint' => env('FRANKENPHP_METRICS_ENDPOINT', '/metrics'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Configure logging for FrankenPHP operations and worker management.
    |
    */
    'logging' => [
        'level' => env('FRANKENPHP_LOG_LEVEL', 'info'),
        'channel' => env('FRANKENPHP_LOG_CHANNEL', 'stack'),
    ],
];
