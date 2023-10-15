<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'ClearCache Recursive',
    'description' => 'ClearCache Recursive',
    'category' => 'plugin',
    'author' => 'Guido Schmechel',
    'author_email' => 'info@ayacoo.de',
    'state' => 'stable',
    'version' => '2.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4'
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
