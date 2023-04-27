<?php

return function ($kirby, $page, $site) {

    # Grab the data from the default global site controller
    $site_vars = $kirby->controller('site', compact('page', 'kirby'));

    if (!$site_vars['authenticated']) {
        go($kirby->url() . '/login');
    }

    # ----------- HANDLE DATA FOR LEADER
    if ($kirby->request()->is('POST') && get('save-user')) {

        $data = get();

        $formRules = [
            'fullname'  => ['required'],
        ];

        $messages = [
            'fullname'  => 'Bitte einen Name eingeben.',
        ];

        // some of the data is invalid
        if ($invalid = invalid($data, $formRules, $messages)) {
            $alert = $invalid;
        } else {

            // everything is ok, handle information
            try {
                $data['title'] = $data['fullname'];
                unset($data['save-user']);

                $updatedLeader = $page->update($data);

                if ($updatedLeader) {
                    $alert[] = 'Benutzer erfolgreich gespeichert!';

                    unset($data['title']);
                }
            } catch (Exception $e) {
                $alert[] = 'Es ist ein Fehler aufgetreten: ' . $e->getMessage();
            }
        }
    }

    # ----------- HANDLE RESET PIN
    if ($kirby->request()->is('POST') && get('reset-pin')) {

        $data = get();

        try {
            $curatorPage = $page->parent()->find($data['curator-id']);
            $curatorPageUpdated = $curatorPage->update([
                'pin' => ''
            ]);

            if ($curatorPageUpdated) {
                $alert[] = 'PIN zurückgesetzt.';
            }
        } catch (Exception $e) {
            $alert[] = 'Ein Fehler ist augetretten. ' . $e->getMessage();
        }
    }


    # ----------- HANDLE SEND WORKSHOP
    if ($kirby->request()->is('POST') && get('send-ws')) {

        $data = get();

        // everything is ok, handle information
        try {
            unset($data['send-ws']);

            $updatedWS = $page->parent()->update([
                'complete' => true,
                'comment' => $data['ws-comment']
            ]);

            $alert[] = "Das Workshop wurde erfolgreich als fertig markiert.";
        } catch (Exception $e) {
            $alert[] = 'Your registration failed: ' . $e->getMessage();
        }

        // sends the workshop via email
        $mailData = [
            'ws-name' => 'Workshop Name (ID): ' . $page->parent()->title() . '(' . $page->parent()->slug() . ')',
            'ws-participants'   => $page->parent()->childrenAndDrafts()->filterBy('intendedTemplate', 'c_curator')->sortBy('title'),
            'ws-exhibits'       => $page->parent()->childrenAndDrafts()->filterBy('intendedTemplate', 'c_exhibit')->sortBy('title'),
            'ws-exhibitions'    => $page->parent()->childrenAndDrafts()->filterBy('intendedTemplate', 'c_exhibition')->sortBy('title'),
            'ws-leader'         => $page->title(),
            'ws-comment'        => $data['ws-comment'],
            'ws-notDoneParticipants' => $data['notDoneParticipants'],
            'ws-notDoneExhibits' => $data['notDoneExhibits'],
            'ws-notDoneExhibitions' => $data['notDoneExhibitions'],
        ];

        $mailRules = [
            'from_address'    => ['required'],
            'to_address'      => ['required'],
        ];

        $mailMessages = [
            'from_address'  => 'Absender Email wurde nicht konfiguriert. Bitte wenden Sie sich an das Museum.',
            'to_address'     => 'Empfänger Email wurde nicht konfiguriert. Bitte wenden Sie sich an das Museum.',
        ];

        // some of the data is invalid
        if ($invalidMails = invalid($site->content()->toArray(), $mailRules, $mailMessages)) {
            $alert = $invalidMails;
        } else {
            try {
                $kirby->email([
                    'beforeSend' => function ($mailer) {
                        $usernameSmtp = option('jds.mailsettings.un') ?? null;
                        $passwordSmtp = option('jds.mailsettings.pw') ?? null;
                        $host = option('jds.mailsettings.host') ?? null;
                        $port = (int) option('jds.mailsettings.port') ?? null;

                        if ($usernameSmtp)
                            $mailer->Username   = $usernameSmtp;
                        if ($passwordSmtp)
                            $mailer->Password   = $passwordSmtp;
                        if ($host)
                            $mailer->Host       = $host;
                        if ($port)
                            $mailer->Port       = $port;
                        if ($usernameSmtp && $passwordSmtp) {
                            $mailer->isSMTP();
                            $mailer->SMTPAuth   = true;
                        }

                        return $mailer;
                    },

                    'from' => $site->from_address()->value(),
                    'replyTo' => $site->from_address()->value(),
                    'to' => $site->to_address()->value(),
                    'cc' => $site->cc_address()->isNotEmpty() ? $site->cc_address()->value() :  null,
                    'subject' => ($site->mail_subject()->isNotEmpty() ? $site->mail_subject()->value() :  'Neuer Workshop')  . ' :: [' . $mailData['ws-name'] . ']',
                    'template' => 'ws_notification',
                    'data'  => $mailData
                ]);

                $alert[] =  "Das Museum wurde erfolgreich benachrichtigt.";
            } catch (Exception $error) {
                kirbylog("Der Mailversand ist fehlgeschlagen: " . $error);
                $alert[] =  "Der Mailversand ist fehlgeschlagen: " . $error;
            }
        }
    }

    # ----------- HANDLE CREATE EXHIBITION
    if ($kirby->request()->is('POST') && get('create-exhibition')) {
        $data = get();
        $data['modifiedByUser'] = $kirby->user()->name();
        $data['tempslug'] = $data['exhibitiontitle'];


        /* workaround -> we check for these messages here already with handleExhibitionMessages,
        even though exhibition model is going to recheck, because otherwise PHP will render
        the exhibit content already while updates to the messages are still happening in the back.
        This way the messages are created before the page is created and */
        /*         $exhibition_msgs = handleExhibitionMessages($data, $page);
        $data = array_merge($data, $exhibition_msgs); */

        // TODO: --> this is still not working, sadly. Maybe update Exhibition List via ajax instead?
        /* After creating an exhibition the leader has to refresh once to see the actual
        hints. Not a huge problem, but would be nice to do it somehow else.*/

        $formRules = [
            'exhibitiontitle'    => ['required'],
            'impulse'            => ['required'],
        ];

        $messages = [
            'exhibitiontitle '  => 'Bitte geben Sie einen Name ein.',
            'impulse'           => 'Bitte geben Sie ein Thema ein.',
        ];

        // some of the data is invalid
        if ($invalid = invalid($data, $formRules, $messages)) {
            $alert = $invalid;
        } else {
            try {
                // create exhibition
                $data['title'] = $data['exhibitiontitle'];
                unset($data['create-exhibition']);

                $exhibition = $page->parent()->createChild([
                    'slug'     => time(),
                    'template' => 'c_exhibition',
                    'content'  => $data
                ]);

                if ($exhibition) {
                    $alert[] = 'Ausstellung erfolgreich angelegt!';
                    unset($data['title']);
                }
            } catch (Exception $e) {
                $alert = ['Ausstellungs-Registrierung fehlgeschlagen: ' . $e->getMessage()];
            }
        }
    }

    # ----------- HANDLE EDIT EXHIBITION
    if ($kirby->request()->is('POST') && get('save-exhibition')) {

        $data = get();
        $data['modifiedByUser'] = $kirby->user()->name();

        $currentExhibition = $page->parent()->childrenAndDrafts()->find($data['exhibition-id']);

        $formRules = [
            'exhibitiontitle'    => ['required'],
            'impulse'            => ['required'],
        ];

        $messages = [
            'exhibitiontitle '  => 'Bitte geben Sie einen Name ein.',
            'impulse'           => 'Bitte geben Sie ein Thema ein.',
        ];

        // some of the data is invalid
        if ($invalid = invalid($data, $formRules, $messages)) {
            $alert = $invalid;
        } else {
            // everything is ok, handle information
            try {
                $data['title'] = $data['exhibitiontitle'];
                unset($data['save-exhibition']);

                $changeSlug = $currentExhibition->title() != $data['title'];

                $updateexhibition = $currentExhibition->update($data);

                if ($updateexhibition) {
                    // store referer and name in session
                    $kirby->session()->set([
                        'referer' => $page->uri(),
                        'regName'  => esc($page->title()),
                        'uname' => $kirby->user()->name()
                    ]);

                    $updateexhibition->unlockMe();

                    if ($changeSlug) {
                        $indexedSlug = $updateexhibition->checkSlugIndex(Str::slug($data['title']));
                        $updateexhibition->changeSlugOnly($indexedSlug);
                    }

                    $alert[] = 'Ausstellung erfolgreich gespeichert!';

                    //$data['exhibitiontitle'] = $data['title'];
                    unset($data['title']);
                }
            } catch (Exception $e) {
                $alert[] = 'Es ist ein Fehler aufgetreten: ' . $e->getMessage();
            }
        }
    }

    # ----------- HANDLE DELETE EXHIBITION
    if ($kirby->request()->is('POST') && get('delete-exhibition')) {

        $data = get();

        try {
            unset($data['delete-exhibition']);

            $currentExhibition = $page->parent()->childrenAndDrafts()->find($data['exhibition-id']);
            $deleteexhibition = $currentExhibition->delete();

            if ($deleteexhibition) {
                // store referer and name in session
                $kirby->session()->set([
                    'referer' => $page->uri(),
                    'regName'  => esc($page->title()),
                    'uname' => $kirby->user()->name()
                ]);

                $alert[] = 'Ausstellung erfolgreich gelöscht!';
            }
        } catch (Exception $e) {
            $alert[] = 'Es ist ein Fehler aufgetreten: ' . $e->getMessage();
        }
    }

    // logout in case it was auto-logout (javascript called)
    if ($kirby->request()->is('POST') && get('logout-after-save')) {
        go($kirby->url() . '/logout');
    }

    // return data to template
    return [
        'alert'         => $alert ?? null,
        'data'          => $data ?? false,
        'authenticated' => $site_vars['authenticated'] ?? false,
        'dataPage'      => $site_vars['dataPage'],
    ];
};
