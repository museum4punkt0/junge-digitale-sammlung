<?php

return [
    'leaderpinreset' => function (Kirby\Cms\Page $page = null, string $data = null) {
        // $page => page object  
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
        // $page => page object  
        // $data => 'my custom data'
        buildUsersTree($page, $data);
        $amount = explode('&', $data);
        return [
            'status' => 200,
            'label' => (int)$amount[0] + (int)$amount[1] . " Benutzer angelegt.",
        ];
    },

    'deactivateusers' => function (Kirby\Cms\Page $page = null, string $data = null) {
        // $page => page object  
        // $data => 'my custom data'
        $users = kirby()->users()->role('frontenduser');
        $amount = 0;

        kirbylog('---- CRON DEACTIVATE USERS: ' . date('Y-m-d H:i:s') . ' ----');

        kirby()->impersonate('kirby');

        foreach ($users as $u) {
            $deadline =  new Kirby\Toolkit\Date($u->expiration());
            $now = new Datetime(date('Y-m-d'));
            $endDate = new Datetime($deadline);
            $diff = $endDate->diff($now);

            if (
                $u->active()->toBool() &&
                $now > $endDate
            ) {
                $u->update([
                    'active' => false
                ]);
                kirbylog($u->name()->value() . ' was overdue for ' . $diff->days . ' days');
                kirbylog('--');
                $amount++;
            }
        }

        kirbylog('RESULT: ' . $amount . " users deactivated");
        kirbylog('---------------------------------------------');
        kirbylog(' ');

        kirby()->impersonate('nobody');

        return [
            'status' => 200,
            'label' => $amount . " Benutzer deaktiviert",
        ];
    },

    'deleteusers' => function (Kirby\Cms\Page $page = null, string $data = null) {
        // $page => page object  
        // $data => 'my custom data'
        $users = kirby()->users()->role('frontenduser');
        $amount = 0;

        kirbylog('---- CRON DELETE USERS: ' . date('Y-m-d H:i:s') . ' ----');

        kirby()->impersonate('kirby');
        $msg = '';
        foreach ($users as $u) {
            $deadline =  new Kirby\Toolkit\Date($u->expiration());
            $deadline = $deadline->add(new DateInterval('P40D')); // 40 days

            $now = new Datetime(date('Y-m-d'));
            $endDate = new Datetime($deadline);
            $diff = $endDate->diff($now);

            

            if (
                !$u->active()->toBool() &&
                $now  > $endDate
            ) {
                try {
                    $u->delete();
                    kirbylog($u->name()->value() . ' was inactive for ' . $diff->days . ' days');
                    kirbylog('--');
                    $now = new Datetime(date('Y-m-d'));
                    $endDate = new Datetime($u->expiration()->toDate('Y-m-d'));
                } catch (Exception $e) {
                }
                $amount++;
            }
        }

        kirbylog('RESULT: ' . $amount . " users deleted");
        kirbylog('---------------------------------------------');
        kirbylog(' ');
        kirby()->impersonate('nobody');

        return [
            'status' => 200,
            'label' => $amount . " Benutzer gelöscht",
        ];
    },

    'cleanroutine' => function (Kirby\Cms\Page $page = null, string $data = null) {
        // $page => page object  
        // $data => 'my custom data'
        $workshops = site()->childrenAndDrafts()->filterBy('intendedTemplate', 'c_workshop');
        $amount = 0;

        kirbylog('---- CRON DELETE EXHIBITS AND EXHIBITIONS: ' . date('Y-m-d H:i:s') . ' ----');

        kirby()->impersonate('kirby');

        foreach ($workshops as $ws) {
            $amount += removeUnusedPages($ws);
        }

        kirbylog('RESULT: ' . $amount . " exhibits/exhibitions were deleted");
        kirbylog('---------------------------------------------');
        kirbylog(' ');
        kirby()->impersonate('nobody');

        return [
            'status' => 200,
            'label' => $amount . " Objekte und/oder Ausstellungen gelöscht",
        ];
    },

    'workshopassetlinking' => function (Kirby\Cms\Page $page = null, string $data = null) {
        // $page => page object  
        // $data => 'my custom data'
        $amount = bindAllImagesTo3DModels($page);

        kirbylog('CRON: ' . $amount . " Objekt erneut verlinkt in " . $page->title());

        return [
            'status' => 200,
            'label' => $amount . " Objekt erneut verlinkt.",
        ];
    },
];
