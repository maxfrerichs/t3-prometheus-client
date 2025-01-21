<?php

return [
    'frontend' => [
        'mfr/typo3-prometheus' => [
            'target' =>
                \MFR\T3PromClient\Middleware\PrometheusMiddleware::class,
            'before' => [
                'typo3/cms-frontend/site',
                'typo3/cms-frontend/timetracker',
            ],
        ],
    ],
];
