<?php

/**
 * System Functions for CRONs
 * and Janitor plugin. These functions can
 * be called via CRON or via Janitor-Button field.
 * (The Janitor-Plugin in this theme has been changed
 * to handle some special cases, please dont replace it.)
 */

return [

    /**
     * Function gets called via Button inside a User-Page.
     * The admins have with this the possibility of reseting PINs
     * for participants and participant leaders.
     */
    'leaderpinreset' => function (Kirby\Cms\Page $page = null, string $data = null) {
        $leaderUpdated = $page->update([
            'pin' => ''
        ]);
        return [
            'status' => 200,
            'label' => "PIN zurück gesetzt.",
        ];
    },

    /**
     * Function gets called via Button inside a Workshop, Users-Tab.
     * The extra fields where oyu can put your info are customized.
     * The original Janitor-Plugin is only a button with no fields.
     */
    'creategroupusers' => function (Kirby\Cms\Page $page = null, string $data = null) {
        buildUsersTree($page, $data);
        $amount = explode('&', $data);
        return [
            'status' => 200,
            'label' => (int)$amount[0] + (int)$amount[1] . " Benutzer angelegt.",
        ];
    },

    /**
     * Deactives temp-users if they are past deadline.
     */
    'deactivateusers' => function (Kirby\Cms\Page $page = null, string $data = null) {
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
                kirbylog('.');
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

    /**
     * Deletes the temp-users. It fetches the temp-users and
     * checks which ones are past deletion date. The $inactivityDays
     * is read from the Site data (can be changed in the admin-area).
     */
    'deleteusers' => function (Kirby\Cms\Page $page = null, string $data = null) {
        $users = kirby()->users()->role('frontenduser');
        $amount = 0;
        $inactivityDays = site()->userdaysbeforedelete()->isNotEmpty() ? site()->userdaysbeforedelete()->value() : 40;
        
        kirbylog('---- CRON DELETE USERS: ' . date('Y-m-d H:i:s') . ' ----');

        kirby()->impersonate('kirby');
        $msg = '';
        foreach ($users as $u) {
            $deadline =  new Kirby\Toolkit\Date($u->expiration());
            $deadline = $deadline->add(new DateInterval('P' . strval($inactivityDays) . 'D')); 

            $now = new Datetime(date('Y-m-d'));
            $endDate = new Datetime($deadline);
            $diff = $endDate->diff($now);

            if (
                !$u->active()->toBool() &&
                $now  > $endDate
            ) {
                try {

                    if ($ws = $u->linked_workshop()->toPageOrDraft())
                        removeUnusedPages($ws);

                    if ($u->delete()) {
                        kirbylog($u->name()->value() . ' User DELETED. Was inactive for ' . $diff->days . ' days');
                        $amount++;
                    } else
                        kirbylog($u->name()->value() . ' User ERROR DELETING. Has been inactive for ' . $diff->days . ' days');

                    kirbylog('.');
                } catch (Exception $e) {
                    kirbylog($u->name()->value() . ' Workshop ERROR DELETING. Has been inactive for ' . $diff->days . ' days');
                }
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

    /**
     * Cleans the workshop by removing exhibits and exhibitions
     * that 1) are still drafts and 2) have incomplete information.
     * Please refer to the global function 'removeUnusedPages' function
     * for the logic.
     */
    'cleanroutine' => function (Kirby\Cms\Page $page = null, string $data = null) {
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

    /**
     * Relinks preview images of a phsyical objekt to their 3D model file,
     * if it exists. Please refer to the documentation.
     */
    'workshopassetlinking' => function (Kirby\Cms\Page $page = null, string $data = null) {
        $amount = bindAllImagesTo3DModels($page);

        kirbylog('CRON: ' . $amount . " Objekt erneut verlinkt in " . $page->title());

        return [
            'status' => 200,
            'label' => $amount . " Objekt erneut verlinkt.",
        ];
    },
];
