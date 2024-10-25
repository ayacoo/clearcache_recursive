<?php

namespace Ayacoo\ClearCacheRecursive\EventListener;

use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\Components\Buttons\LinkButton;
use TYPO3\CMS\Backend\Template\Components\ModifyButtonBarEvent;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\IconSize;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ModifyButtonBarEventListener
{
    public function __invoke(ModifyButtonBarEvent $event): void
    {
        $request = $GLOBALS['TYPO3_REQUEST'];
        $buttons = $event->getButtons();
        $pageUid = ($request->getQueryParams()['id'] ?? $request->getParsedBody()['id'] ?? 0);
        if ($pageUid > 0) {
            $button = $this->makeCacheButton($event->getButtonBar(), (int) $pageUid);
            $buttons[ButtonBar::BUTTON_POSITION_RIGHT][0][] = $button;
            $event->setButtons($buttons);
        }
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
            $iconFactory->getIcon('clearCacheRecursive', IconSize::SMALL)
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