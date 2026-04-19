<?php

use Ayacoo\ClearCacheRecursive\Controller\BackendController;

return [
    'clearCacheRecursive' => [
        'path' => '/clearcacherecursive',
        'target' => BackendController::class . '::clearCacheRecursive',
    ]
];
