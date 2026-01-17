<?php

namespace Ayacoo\ClearCacheRecursive\Backend\ContextMenu;


use TYPO3\CMS\Backend\ContextMenu\ItemProviders\AbstractProvider;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

class ClearCacheItemProvider extends AbstractProvider
{
    /**
     * @var array
     */
    protected $itemsConfiguration = [
        'clearCacheRecursive' => [
            'type' => 'item',
            'label' => 'LLL:EXT:clearcache_recursive/Resources/Private/Language/locallang.xlf:clearcache.button.title',
            'iconIdentifier' => 'clearCacheRecursive',
            'callbackAction' => 'clearCacheRecursive',
        ],
    ];

    /**
     * Checks if this provider may be called to provide the list of context menu items for given table.
     *
     * @return bool
     */
    public function canHandle(): bool
    {
        return $this->table === 'pages';
    }

    /**
     * Returns the provider priority which is used for determining the order in which providers are processing items
     * to the result array. Highest priority means provider is evaluated first.
     *
     * This item provider should be called after PageProvider which has priority 100.
     *
     * BEWARE: Returned priority should logically not clash with another provider.
     *         Please check @return int
     * @see \TYPO3\CMS\Backend\ContextMenu\ContextMenu::getAvailableProviders() if needed.
     *
     */
    public function getPriority(): int
    {
        return 55;
    }

    /**
     * @param string $itemName
     * @return array
     */
    protected function getAdditionalAttributes(string $itemName): array
    {
        return [
            'data-callback-module' => '@ayacoo/clear-cache-recursive/context-menu-actions',
            'data-test' => 'clearCacheRecursive'
        ];
    }

    /**
     * @param array $items
     * @return array
     */
    public function addItems(array $items): array
    {
        $this->initDisabledItems();
        $localItems = $this->prepareItems($this->itemsConfiguration);
        if (isset($items['clearCache'])) {
            $position = array_search('clearCache', array_keys($items), true);

            $beginning = array_slice($items, 0, $position + 1, true);
            $end = array_slice($items, $position, null, true);

            $items = $beginning + $localItems + $end;
        } else {
            $items = $items + $localItems;
        }
        return $items;
    }

    /**
     * @param string $itemName
     * @param string $type
     * @return bool
     */
    protected function canRender(string $itemName, string $type): bool
    {
        $backendUser = $this->getBackendUserAuthentication();
        if ($itemName === 'clearCacheRecursive') {
            return $backendUser->isAdmin() || (bool)($backendUser->getTSConfig()['options.']['clearCache.']['subpages'] ?? false);
        }
        return true;
    }

    /**
     * @return BackendUserAuthentication
     */
    protected function getBackendUserAuthentication(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}