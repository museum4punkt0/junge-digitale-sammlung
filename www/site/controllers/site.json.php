<?php

return function ($kirby, $page, $site) {

    $lockactionstatus = "";

    /***
     * 
     * LOCK : For Exhibition pages
     * 
     */

    if ($kirby->request()->is('GET') && get('lockMe')) {

        $lockMe = get('lockMe');

        if ($lockMe) {
            $lockMePage = $page->parent()->findPageOrDraft($lockMe);

            if ($lockMePage) {
                if ($lockMePage->checkLock($page->slug())) {
                    $lockactionstatus = "already_locked";
                    $lock = $lockMePage->lock();
                    $lockedBy = $lock->getInfos()['curator'];
                    $overlayCode = snippet('forms_blocks/overlay-blocked', ['lock' => $lock, 'lockMe' => $lockMe, 'page' => $page], true);
                } else {
                    $lockMePage->lockMe($page);
                    $lockactionstatus = "PAGE LOCKED " . $lockMePage->title();
                }
            } else {
                $lockactionstatus = "page not found";
            }
        }
    }

    /***
     * 
     * UNLOCK : For Exhibition pages
     * 
     */
    if ($kirby->request()->is('GET') && get('unlockMe')) {

        $unlockMe = get('unlockMe');
        $blockedBy = get('blockedBy');

        if ($unlockMe) {
            $unlockMePage = $page->parent()->findPageOrDraft($unlockMe);

            if ($unlockMePage) {
                if ($unlockMePage->lock()->getCurator() == $page->slug()) {
                    $unlockMePage->unlockMe();
                    $lockactionstatus = "PAGE UNLOCKED " . $unlockMePage->title();
                } else if ($blockedBy && $blockedBy != $page->slug()) {
                    $lockactionstatus = "was_already_locked";
                    $lockedBy = $blockedBy;
                }
            } else {
                $lockactionstatus = "page not found";
            }
        }
    }

    if ($kirby->request()->is('GET') && get('pageID')) { // Validation for impulse
        $impulse = get('impulseCheck');
        $pageID = get('pageID'); // exhibition

        $exhibit = $page->parent()->findPageOrDraft($pageID);

        if ($exhibit) {
            if ($user = $exhibit->linked_user()->toPageOrDraft()) {
                if ($exhibition = $user->linked_exhibition()->toPageOrDraft()) {
                    // exhibition found, check if impulses match

                    if ($impulse == $exhibition->impulse()->value()) {
                        $impulseResult = 'result_success';
                    } else {
                        $impulseResult = 'result_error';
                    }
                }
                // UUID not found, object not part of any exhibition
                else {
                    $impulseResult = 'UUID keine Ausstellung vorhanden';
                    $impulseResult = 'no_exhibition';
                }
            }
            // UUID not found, object not part of any exhibition
            else {
                $impulseResult = 'UUID Benutzer nicht gefunden';
            }
        }
        // UUID not found, no exhibit found
        else {
            $impulseResult = 'UUID Objekt nicht gefunden';
        }
    }


    // Get embed info for AJAX call
    if ($kirby->request()->is('GET') && get('embedurl')) {
        $embed = [];
        $embedurl = get('embedurl');

        if (!V::url($embedurl)) {
            $embed['status'] = 'error';
            $embed['error']  = 'The $url variable is not an url';
        } else {
            $embed = scrapEmbed($embedurl, $embed);
        }
    }


    if ($kirby->request()->is('GET') && get('exhibitionID')) { // Validation for impulse for curator list in leader
        $curatorID = get('curatorID');
        $exhibitionID = get('exhibitionID');

        $exhibition = $page->parent()->findPageOrDraft($exhibitionID);

        $impulseResult =  matchImpulses($exhibition, $curatorID);
    }

    $dataPage = $site->data_fieldinfos_pick()->toPage();

    return [
        //'relatedExhibits' => $options,
        'lockactionstatus' => $lockactionstatus ?? null,
        'lockedBy' => $lockedBy ?? null,
        'impulseResult' => $impulseResult ?? null,
        'overlayCode' => $overlayCode ?? null,
        'embed' => $embed ?? null,
        'dataPage' => $dataPage ?? null,
        'json' => [],
    ];
};
