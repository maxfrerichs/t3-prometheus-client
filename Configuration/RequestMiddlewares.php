<?php

return [
    'frontend' => [
        'mfr/t3-prometheus-client' => [
            'target' =>
                \MFR\T3PromClient\Middleware\PrometheusMiddleware::class,
            'before' => [
                'typo3/cms-frontend/site',
                'typo3/cms-frontend/timetracker',
            ],
        ],
    ],
];
