<?php

$EM_CONF['t3_prometheus_client'] = [
    'title' => 'Prometheus client for TYPO3',
    'description' => 'Provides a client for Prometheus to aggregate timeseries data from various metrics. Supports both scraping and pushing metrics to pushgateway',
    'category' => 'services',
    'version' => '0.11.1',
    'state' => 'beta',
    'clearCacheOnLoad' => 0,
    'author' => 'Max Frerichs',
    'author_email' => 'typo3@maxfrerichs.dev',
    'author_company' => 'LfdA - Labor für digitale Angelegenheiten GmbH',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-14.3.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
