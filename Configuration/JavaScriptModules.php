<?php

return [
    'dependencies' => ['core', 'backend'],
    'tags' => [
        'backend.contextmenu',
    ],
    'imports' => [
        '@ayacoo/clear-cache-recursive/' => 'EXT:clearcache_recursive/Resources/Public/JavaScript/',
    ],
];
