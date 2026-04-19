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
use TYPO3\CMS\Core\Http\JsonResponse;
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
        $backendUser = $this->getBackendUser();
        if (!$backendUser->isAdmin() && !($backendUser->getTSConfig()['options.']['clearCache.']['subpages'] ?? false)) {
            return new JsonResponse(['success' => false, 'error' => 'Permission denied'], 403);
        }

        $pageUid = (int)($request->getQueryParams()['uid'] ?? $request->getParsedBody()['uid'] ?? 0);
        $ajaxCall = (int)($request->getQueryParams()['ajax'] ?? $request->getParsedBody()['ajax'] ?? 0);

        if ($pageUid > 0) {
            $title = $this->getLanguageService()->sL('clearcache_recursive.messages:clearcache.message.title');
            $message = $this->getLanguageService()->sL('clearcache_recursive.messages:clearcache.message.description');

            $pageUidList = $this->queryGenerator->getTreeList($pageUid, 99);
            $pages = GeneralUtility::intExplode(',', $pageUidList, true) ?? [];
            if (!empty($pages)) {
                $permissionClause = $this->getBackendUser()->getPagePermsClause(Permission::PAGE_SHOW);
                $this->dataHandler->start([], []);

                foreach ($pages as $singlePageUid) {
                    $pageRow = BackendUtility::readPageAccess($singlePageUid, $permissionClause);
                    if ($singlePageUid !== 0 && $this->getBackendUser()->doesUserHaveAccess($pageRow, Permission::PAGE_SHOW)) {
                        $this->dataHandler->clear_cacheCmd($singlePageUid);
                    }
                }
            }

            if (($request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') || str_contains($request->getHeaderLine('Accept'), 'application/json')) {
                return new JsonResponse(['success' => true]);
            }


            if ($ajaxCall === 0) {
                $title = $this->getLanguageService()->sL('LLL:EXT:clearcache_recursive/Resources/Private/Language/locallang.xlf:clearcache.message.title');
                $message = $this->getLanguageService()->sL('LLL:EXT:clearcache_recursive/Resources/Private/Language/locallang.xlf:clearcache.message.description');

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
        }

        if (($request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') || str_contains($request->getHeaderLine('Accept'), 'application/json')) {
            return new JsonResponse(['success' => false, 'error' => 'Invalid UID'], 400);
        }

        if ($ajaxCall === 1) {
            return new JsonResponse(['success' => true]);
        }

        $backendUriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $uriParameters = ['id' => $pageUid];
        $returnLink = $backendUriBuilder->buildUriFromRoute(
            'web_layout',
            $uriParameters
        );

        return new RedirectResponse($returnLink);
    }

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
