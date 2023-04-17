<?php
return function ($page, $kirby, $site) {

    if ($kirby->request()->is('GET') && get('logout')) {
        $page->unlockMe();

        if ($linked_exhibit = $page->linked_exhibit()->toPageOrDraft()) {
            $linked_exhibit->unlockMe();
        }

        if ($linked_exhibition = $page->linked_exhibition()->toPageOrDraft()) {
            $linked_exhibition->unlockMe();
        }

        go($kirby->url() . '/logout');
    }


    if ($kirby->request()->is('POST') && get('force-unlock')) {
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
    //$frontend_sites = ['c_curator', 'c_curator_leader', 'c_exhibit', 'c_workshop', 'c_exhibition'];
    $linkedUser;

    if ($template == 'c_curator_leader' || $template == 'c_curator' || $template == 'c_exhibit' || $template == 'c_exhibition') {
        $linkedUser = $page->parent()->mainuser()->toUser();
    } else if ($template == 'c_workshop') {
        $linkedUser = $page->mainuser()->toUser();
    } else {
        $linkedUser = '';
    }

    //$authenticated = $user && (($user->role()->name() === 'admin')  ||  $linkedUser === $user) && (in_array($template, $frontend_sites));
    $authenticated = $user && (($user->role()->name() === 'admin')  ||  $ws) /* && (in_array($template, $frontend_sites)) */;

    $dataPage = site()->data_fieldinfos_pick()->toPage();

    # Return an array containing the data that we want to pass to the template
    return [
        'authenticated' => $authenticated ?? false,
        'linkedUser' => $user ?? '',
        'dataPage' => $dataPage,
        //'feUser'=> $user->name()
    ];
};
