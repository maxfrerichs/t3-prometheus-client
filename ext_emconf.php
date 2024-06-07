<?php

$EM_CONF['typo3_prometheus'] = [
    'title' => 'TYPO3 Prometheus',
    'description' => 'Generates Prometheus-readable metrics from TYPO3 system status reports',
    'category' => 'services',
    'version' => '1.0.0',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'author' => 'Max Frerichs',
    'author_email' => 'typo3@maxfrerichs.dev',
    'author_company' => 'LfdA - Labor für digitale Angelegenheiten GmbH',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-13.99.99',
            'cms-reports' => '11.5.0-13.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
