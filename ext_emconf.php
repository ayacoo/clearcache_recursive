<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'ClearCache Recursive',
    'description' => 'Adds the option in the backend for editors to be able to delete the cache recursively.',
    'category' => 'plugin',
    'author' => 'Guido Schmechel',
    'author_email' => 'info@ayacoo.de',
    'state' => 'stable',
    'version' => '4.0.0',
    'constraints' => [
        'depends' => [
            'php' => '8.2.0-8.5.99',
            'typo3' => '14.3.0-14.3.99',
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
