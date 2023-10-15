<?php

declare(strict_types=1);

namespace Ayacoo\ClearCacheRecursive\Hooks;

use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\Components\Buttons\LinkButton;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ButtonBarHook
{
    /**
     * Get buttons
     *
     * @param array $params
     * @param ButtonBar $buttonBar
     *
     * @return array
     * @throws RouteNotFoundException
     */
    public function getButtons(array $params, ButtonBar $buttonBar): array
    {
        $buttons = $params['buttons'];
        $pageUid = (int)(GeneralUtility::_GET('id') ?? 0);
        /** @var LinkButton $firstButton */
        $firstButton = $buttons[ButtonBar::BUTTON_POSITION_RIGHT][1][0] ?? null;
        if ($pageUid > 0 && !is_null($firstButton) && $firstButton->getClasses() === 't3js-clear-page-cache') {
            $button = $this->makeCacheButton($buttonBar, $pageUid);
            $buttons[ButtonBar::BUTTON_POSITION_RIGHT][10][] = $button;
        }
        return $buttons;
    }

    /**
     * @param ButtonBar $buttonBar
     * @param int $pageUid
     * @return LinkButton
     * @throws RouteNotFoundException
     */
    protected function makeCacheButton(ButtonBar $buttonBar, int $pageUid): LinkButton
    {
        $title = $this->getLanguageService()->sL('LLL:EXT:clearcache_recursive/Resources/Private/Language/locallang.xlf:clearcache.button.title');

        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $button = $buttonBar->makeLinkButton();
        $button->setIcon(
            $iconFactory->getIcon('clearCacheRecursive', Icon::SIZE_SMALL)
        );
        $button->setTitle($title);

        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $uri = $uriBuilder->buildUriFromRoute(
            'clearCacheRecursive',
            ['uid' => $pageUid]
        );
        $button->setHref($uri);

        return $button;
    }

    /**
     * Returns LanguageService
     *
     * @return LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
