<?php

return [
    'leaderpinreset' => function (Kirby\Cms\Page $page = null, string $data = null) {
        // $page => page object where the button as pressed
        // $data => 'my custom data'
        $leaderUpdated = $page->update([
            'pin' => ''
        ]);
        return [
            'status' => 200,
            'label' => "PIN zurück gesetzt.",
        ];
    },

    'creategroupusers' => function (Kirby\Cms\Page $page = null, string $data = null) {
        // $page => page object where the button as pressed
        // $data => 'my custom data'
        buildUsersTree($page, $data);
        $amount = explode('&', $data);
        return [
            'status' => 200,
            'label' => (int)$amount[0] + (int)$amount[1] . " Benutzer angelegt.",
        ];
    },

    'deactivateusers' => function (Kirby\Cms\Page $page = null, string $data = null) {
        // $page => page object where the button as pressed
        // $data => 'my custom data'
        $users = kirby()->users()->role('frontenduser');
        $amount = 0;

        kirby()->impersonate('kirby');

        foreach ($users as $u) {
            if ($u->active()->toBool() && date('Y-m-d', time()) > $u->expiration()->toDate('Y-m-d')) {
                $u->update([
                    'active' => false
                ]);
                $amount++;
            }
        }

        kirbylog('CRON: ' . $amount . " Benutzer deaktiviert");
        kirby()->impersonate('nobody');

        return [
            'status' => 200,
            'label' => $amount . " Benutzer deaktiviert",
        ];
    },

    'deleteusers' => function (Kirby\Cms\Page $page = null, string $data = null) {
        // $page => page object where the button as pressed
        // $data => 'my custom data'
        $users = kirby()->users()->role('frontenduser');
        $amount = 0;

        kirby()->impersonate('kirby');
        $msg = '';
        foreach ($users as $u) {
            $deadline =  new Kirby\Toolkit\Date($u->expiration());
            $deadline = $deadline->add(new DateInterval('P7D'));

            if (!$u->active()->toBool() && date('Y-m-d', time()) > $deadline->format('Y-m-d')) {
                try {
                    $u->delete();
                } catch (Exception $e) {
                }
                $amount++;
            }
        }

        kirbylog('CRON: ' . $amount . " Benutzer gelöscht");
        kirby()->impersonate('nobody');

        return [
            'status' => 200,
            'label' => $amount . " Benutzer gelöscht",
        ];
    },

    'cleanroutine' => function (Kirby\Cms\Page $page = null, string $data = null) {
        // $page => page object where the button as pressed
        // $data => 'my custom data'
        $workshops = site()->childrenAndDrafts()->filterBy('intendedTemplate', 'c_workshop');
        $amount = 0;

        kirby()->impersonate('kirby');

        foreach ($workshops as $ws) {
            $amount += removeUnusedPages($ws);
        }

        kirbylog('CRON: ' . $amount . " Objekte und/oder Ausstellungen gelöscht");
        kirby()->impersonate('nobody');

        return [
            'status' => 200,
            'label' => $amount . " Objekte und/oder Ausstellungen gelöscht",
        ];
    },

    'workshopassetlinking' => function (Kirby\Cms\Page $page = null, string $data = null) {
        // $page => page object where the button as pressed
        // $data => 'my custom data'
        $amount = bindAllImagesTo3DModels($page);

        kirbylog('CRON: ' . $amount . " Objekt erneut verlinkt in " . $page->title());

        return [
            'status' => 200,
            'label' => $amount . " Objekt erneut verlinkt.",
        ];
    },
];
