<?php

namespace Ayacoo\ClearCacheRecursive\EventListener;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\Components\Buttons\LinkButton;
use TYPO3\CMS\Backend\Template\Components\ComponentFactory;
use TYPO3\CMS\Backend\Template\Components\ModifyButtonBarEvent;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\IconSize;
use TYPO3\CMS\Core\Localization\LanguageService;

class ModifyButtonBarEventListener
{
    public function __construct(
        protected ComponentFactory $componentFactory,
        protected IconFactory $iconFactory,
        protected UriBuilder $uriBuilder
    ) {}

    #[AsEventListener(identifier: 'ayacoo/clear-cache-recursive/modify-button-bar')]
    public function __invoke(ModifyButtonBarEvent $event): void
    {
        if (!$this->isSubpagesClearCacheEnabled()) {
            return;
        }

        $request = $GLOBALS['TYPO3_REQUEST'];
        $buttons = $event->getButtons();
        $pageUid = ($request->getQueryParams()['id'] ?? $request->getParsedBody()['id'] ?? 0);
        if ($pageUid > 0) {
            $button = $this->makeCacheButton((int) $pageUid);
            if (!isset($buttons[ButtonBar::BUTTON_POSITION_RIGHT][1])) {
                $buttons[ButtonBar::BUTTON_POSITION_RIGHT][1] = [];
            }
            array_splice($buttons[ButtonBar::BUTTON_POSITION_RIGHT][1], 1, 0, [$button]);
            $event->setButtons($buttons);
        }
    }

    protected function makeCacheButton(int $pageUid): LinkButton
    {
        $title = $this->getLanguageService()->sL('clearcache_recursive.messages:clearcache.button.title');

        $button = $this->componentFactory->createLinkButton();
        $iconMarkup = $this->iconFactory->getIcon('clearCacheRecursive', IconSize::SMALL)->render('inline');
        $button->setIcon(
            $this->iconFactory->getIcon('clearCacheRecursive', IconSize::SMALL)->setMarkup($iconMarkup)
        );
        $button->setTitle($title);

        $uri = $this->uriBuilder->buildUriFromRoute(
            'clearCacheRecursive',
            ['uid' => $pageUid]
        );
        $button->setHref($uri);

        return $button;
    }

    /**
     * @return bool
     */
    protected function isSubpagesClearCacheEnabled(): bool
    {
        return $this->getBackendUser()->isAdmin() || ($this->getBackendUser()->getTSConfig()['options.']['clearCache.']['subpages'] ?? false);
    }

    /**
     * @return BackendUserAuthentication
     */
    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
