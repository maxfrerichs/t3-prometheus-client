<?php

$EM_CONF['t3_prometheus_client'] = [
    'title' => 'Prometheus client for TYPO3',
    'description' => 'Provides a client for Prometheus to scrape timeseries data from system status informations (more info on "How it works") and exposes them on a configuralble endpoint',
    'category' => 'services',
    'version' => '1.0.0-rc1',
    'state' => 'beta',
    'clearCacheOnLoad' => 0,
    'author' => 'Max Frerichs',
    'author_email' => 'maxfrerichs@gmx.de',
    'author_company' => 'LfdA - Labor für digitale Angelegenheiten GmbH',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-13.4.99',
            'cms-reports' => '12.4.0-13.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
