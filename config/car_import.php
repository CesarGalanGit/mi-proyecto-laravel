<?php

return [
    'request_timeout' => (int) env('CAR_IMPORT_TIMEOUT', 12),

    'connectors' => [
        'wallapop' => [
            'search_url' => env('CAR_WALLAPOP_SEARCH_URL', 'https://es.wallapop.com/app/search?keywords=coche&category_ids=100'),
        ],
        'cochesnet' => [
            'search_url' => env('CAR_COCHESNET_SEARCH_URL', 'https://www.coches.net/segunda-mano/'),
        ],
        'autoscout24' => [
            'search_url' => env('CAR_AUTOSCOUT24_SEARCH_URL', 'https://www.autoscout24.es/lst?atype=C&desc=0&sort=standard'),
        ],
        'milanuncios' => [
            'search_url' => env('CAR_MILANUNCIOS_SEARCH_URL', 'https://www.milanuncios.com/coches-de-segunda-mano/'),
        ],
    ],
];
