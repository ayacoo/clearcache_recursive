<?php

declare(strict_types=1);

namespace Ayacoo\ClearCacheRecursive\Controller;


use Ayacoo\ClearCacheRecursive\Database\QueryGenerator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BackendController
{
    protected DataHandler $dataHandler;

    private QueryGenerator $queryGenerator;

    /**
     * ClearPageCacheController constructor.
     */
    public function __construct()
    {
        $this->dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $this->queryGenerator = GeneralUtility::makeInstance(QueryGenerator::class);
    }

    public function clearCacheRecursive(ServerRequestInterface $request): ResponseInterface
    {
        $pageUid = (int)$request->getQueryParams()['uid'];

        if ($pageUid > 0) {
            $title = $this->getLanguageService()->sL('LLL:EXT:clearcache_recursive/Resources/Private/Language/locallang.xlf:clearcache.message.title');
            $message = $this->getLanguageService()->sL('LLL:EXT:clearcache_recursive/Resources/Private/Language/locallang.xlf:clearcache.message.description');

            $pageUidList = $this->queryGenerator->getTreeList($pageUid, 99);
            $pages = GeneralUtility::intExplode(',', $pageUidList, true) ?? [];
            if (!empty($pages)) {
                $permissionClause = $this->getBackendUserAuthentication()->getPagePermsClause(Permission::PAGE_SHOW);
                $this->dataHandler->start([], []);

                foreach ($pages as $pageUid) {
                    $pageRow = BackendUtility::readPageAccess($pageUid, $permissionClause);
                    if ($pageUid !== 0 && $this->getBackendUserAuthentication()->doesUserHaveAccess($pageRow, Permission::PAGE_SHOW)) {
                        $this->dataHandler->clear_cacheCmd($pageUid);
                    }
                }
            }
        }

        $backendUriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $uriParameters = ['id' => $pageUid];
        $editBookLink = $backendUriBuilder->buildUriFromRoute(
            'web_layout',
            $uriParameters
        );

        $message = GeneralUtility::makeInstance(FlashMessage::class,
            $message,
            $title,
            FlashMessage::OK,
            true
        );

        $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
        $messageQueue = $flashMessageService->getMessageQueueByIdentifier();
        $messageQueue->addMessage($message);

        return new RedirectResponse($editBookLink);
    }

    /**
     * @return BackendUserAuthentication
     */
    protected function getBackendUserAuthentication(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
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
