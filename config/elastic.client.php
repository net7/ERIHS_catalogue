<?php declare(strict_types=1);

return [
    'default' => env('ELASTIC_CONNECTION', 'default'),
    'connections' => [
        'default' => [
            'hosts' => [
                env('ELASTIC_HOST', 'localhost:9200'),
            ],
            'basicAuthentication' => [
                env('ELASTIC_USER'),
                env('ELASTIC_PASS'),
            ],
            'httpClientOptions' => [
                ['verify' => true]
            ],
        ],
    ],
];
