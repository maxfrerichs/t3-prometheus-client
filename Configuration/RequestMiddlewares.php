<?php
return [
    "frontend" => [
        "mfr/typo3-prometheus" => [
            "target" =>
                \MFR\Typo3Prometheus\Middleware\PrometheusMiddleware::class,
            "before" => ["typo3/cms-frontend/site"],
        ],
    ],
];
