/**
 * Module: @ayacoo/clear-cache-recursive/context-menu-actions
 */

import AjaxRequest from "@typo3/core/ajax/ajax-request.js"
import Notification from "@typo3/backend/notification.js";

class ContextMenuActions {

    clearCacheRecursive(table, uid) {
        if (table === 'pages') {
            const url = TYPO3.settings.ajaxUrls.clearCacheRecursive;
            const payload = {
                uid: uid,
                ajax: 1
            };

            new AjaxRequest(url)
                .post(payload).then(async function (response) {
                    const data = await response.resolve();
                    if (data.success === true) {
                        Notification.success(
                            TYPO3.lang['clearcache.message.title'] || 'Clear cache recursive',
                            TYPO3.lang['clearcache.message.description'] || 'The cache for this and all subpages was cleared.'
                        );
                    } else {
                        Notification.error(
                            TYPO3.lang['clearcache.message.error'] || 'Clear cache recursive',
                            data.error || 'An error occurred while clearing the cache.'
                        );
                    }
                }, function (error) {
                    Notification.error(
                        'Error',
                        error.response.status + ' ' + error.response.statusText
                    );
                });
        }
    };
}

export default new ContextMenuActions();