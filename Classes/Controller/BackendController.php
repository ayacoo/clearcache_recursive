<?php

declare(strict_types=1);

namespace Ayacoo\ClearCacheRecursive\Controller;


use Ayacoo\ClearCacheRecursive\Database\QueryGenerator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BackendController
{
    public function __construct(
        protected DataHandler $dataHandler,
        protected QueryGenerator $queryGenerator
    )
    { }

    public function clearCacheRecursive(ServerRequestInterface $request): ResponseInterface
    {
        $pageUid = (int)$request->getQueryParams()['uid'];

        if ($pageUid > 0) {
            $title = $this->getLanguageService()->sL('clearcache_recursive.messages:clearcache.message.title');
            $message = $this->getLanguageService()->sL('clearcache_recursive.messages:clearcache.message.description');

            $pageUidList = $this->queryGenerator->getTreeList($pageUid, 99);
            $pages = GeneralUtility::intExplode(',', $pageUidList, true) ?? [];
            if (!empty($pages)) {
                $permissionClause = $this->getBackendUserAuthentication()->getPagePermsClause(Permission::PAGE_SHOW);
                $this->dataHandler->start([], []);

                foreach ($pages as $singlePageUid) {
                    $pageRow = BackendUtility::readPageAccess($singlePageUid, $permissionClause);
                    if ($singlePageUid !== 0 && $this->getBackendUserAuthentication()->doesUserHaveAccess($pageRow, Permission::PAGE_SHOW)) {
                        $this->dataHandler->clear_cacheCmd($singlePageUid);
                    }
                }
            }

            $message = GeneralUtility::makeInstance(FlashMessage::class,
                $message,
                $title,
                ContextualFeedbackSeverity::OK,
                true
            );

            $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
            $messageQueue = $flashMessageService->getMessageQueueByIdentifier();
            $messageQueue->addMessage($message);
        }

        $backendUriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $uriParameters = ['id' => $pageUid];
        $returnLink = $backendUriBuilder->buildUriFromRoute(
            'web_layout',
            $uriParameters
        );

        return new RedirectResponse($returnLink);
    }

    protected function getBackendUserAuthentication(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
