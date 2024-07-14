<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'ClearCache Recursive',
    'description' => 'ClearCache Recursive',
    'category' => 'plugin',
    'author' => 'Guido Schmechel',
    'author_email' => 'info@ayacoo.de',
    'state' => 'stable',
    'version' => '3.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.0.0-13.4.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],

    'autoload' => [
        'psr-4' => [
            'Ayacoo\\ClearCacheRecursive\\' => 'Classes/',
        ],
    ],
];
