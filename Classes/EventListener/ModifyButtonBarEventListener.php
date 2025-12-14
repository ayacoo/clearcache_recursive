<?php

namespace Ayacoo\ClearCacheRecursive\EventListener;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\Components\Buttons\LinkButton;
use TYPO3\CMS\Backend\Template\Components\ComponentFactory;
use TYPO3\CMS\Backend\Template\Components\ModifyButtonBarEvent;
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
        $request = $GLOBALS['TYPO3_REQUEST'];
        $buttons = $event->getButtons();
        $pageUid = ($request->getQueryParams()['id'] ?? $request->getParsedBody()['id'] ?? 0);
        if ($pageUid > 0) {
            $button = $this->makeCacheButton((int) $pageUid);
            $buttons[ButtonBar::BUTTON_POSITION_RIGHT][1][] = $button;
            $event->setButtons($buttons);
        }
    }

    protected function makeCacheButton(int $pageUid): LinkButton
    {
        $title = $this->getLanguageService()->sL('clearcache_recursive.messages:clearcache.button.title');

        $button = $this->componentFactory->createLinkButton();
        $button->setIcon(
            $this->iconFactory->getIcon('clearCacheRecursive', IconSize::SMALL)
        );
        $button->setTitle($title);

        $uri = $this->uriBuilder->buildUriFromRoute(
            'clearCacheRecursive',
            ['uid' => $pageUid]
        );
        $button->setHref($uri);

        return $button;
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}