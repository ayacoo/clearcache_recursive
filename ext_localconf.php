<?php

use Ayacoo\ClearCacheRecursive\Hooks\ButtonBarHook;

if (!defined('TYPO3')) {
    die('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['Backend\Template\Components\ButtonBar']['getButtonsHook']['ayacoo_clearcacherecursive'] = ButtonBarHook::class . '->getButtons';
