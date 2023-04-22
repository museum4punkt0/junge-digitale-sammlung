<?php
return function ($page, $kirby, $site) {

    if ($kirby->request()->is('GET') && get('logout')) { // logs users out

        // unlocks to current page, if locked
        $page->unlockMe();

        if ($linked_exhibit = $page->linked_exhibit()->toPageOrDraft()) {
            // unlocks my exhibit page, if the current page had an exhibit related to it
            $linked_exhibit->unlockMe();
        }

        if ($linked_exhibition = $page->linked_exhibition()->toPageOrDraft()) {
            // unlocks my exhibition page, if the current page had an exhibit related to it
            $linked_exhibition->unlockMe();
        }

        go($kirby->url() . '/logout');
    }


    if ($kirby->request()->is('POST') && get('force-unlock')) { // force unlocks an exhibition in case a user left it locked
        $forceunlock = get('force-unlock');
        $targetpage = get('targetpage');

        if ($forceunlock && $targetpage) {
            $unlockMePage = $page->parent()->findPageOrDraft($targetpage);

            if ($unlockMePage) {
                $unlockMePage->unlockMe();
                $lockactionstatus = "PAGE UNLOCKED " . $unlockMePage->title();
            } else {
                $lockactionstatus = "page not found";
            }
        }
    }

    $user = $kirby->user();
    $ws = $user ? $user->linked_workshop()->toPageOrDraft() : false;
    $template = $page->intendedTemplate();
    $linkedUser;

    if ($template == 'c_curator_leader' || $template == 'c_curator' || $template == 'c_exhibit' || $template == 'c_exhibition') {
        $linkedUser = $page->parent()->mainuser()->toUser();
    } else if ($template == 'c_workshop') {
        $linkedUser = $page->mainuser()->toUser();
    } else {
        $linkedUser = '';
    }

    $authenticated = $user && (($user->role()->name() === 'admin')  ||  $ws);

    $dataPage = site()->data_fieldinfos_pick()->toPage();

    # Return an array containing the data that we want to pass to the template
    return [
        'authenticated' => $authenticated ?? false,
        'linkedUser' => $user ?? '',
        'dataPage' => $dataPage,
    ];
};
