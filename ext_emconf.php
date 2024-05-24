<?php

$EM_CONF['typo3_prometheus'] = [
    'title' => 'TYPO3 Prometheus',
    'description' => 'Generates Prometheus-readable metrics from TYPO3 system status reports',
    'category' => 'services',
    'version' => '0.2.1',
    'state' => 'beta',
    'clearCacheOnLoad' => 0,
    'author' => 'Max Frerichs',
    'author_email' => 'typo3@maxfrerichs.dev',
    'author_company' => '',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-13.99.99',
            'cms-reports' => '12.4.0-13.99.99',
            'cms-reactions' => '12.4.0-13.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
