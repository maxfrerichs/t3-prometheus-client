<?php

$EM_CONF['t3_prometheus_client'] = [
    'title' => 'Prometheus client for TYPO3',
    'description' => 'Generates Prometheus-readable metrics from TYPO3 system status reports',
    'category' => 'services',
    'version' => '0.3.0',
    'state' => 'beta',
    'clearCacheOnLoad' => 0,
    'author' => 'Max Frerichs',
    'author_email' => 'typo3@maxfrerichs.dev',
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
