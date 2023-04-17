<?php

return function ($kirby, $page) {

    # Grab the data from the default controller for authentification
    $site_vars = $kirby->controller('site', compact('page', 'kirby'));

    if (!$site_vars['authenticated']) {
        go($kirby->url() . '/login');
    }

    # ----------- HANDLE USER PAGE REQUEST
    if ($kirby->request()->is('GET') && get('changeuser')) {
        $alert = $page->unpinMe($page);
    }

    # ----------- HANDLE USER PAGE REQUEST
    if ($kirby->request()->is('POST') && get('search-participant')) {

        $data = get();

        $rules = [
            'url_first_part'     => ['alphanum', 'required'],
            'url_second_part'    => ['alphanum', 'required'],
            'url_third_part'     => ['alphanum', 'required'],
            'url_fourth_part'     => ['alphanum', 'required'],
        ];

        $messages = [
            'url_first_part'    => 'Bitte ein gültiges Zeichen eingeben: <a href="#url_first_part">ID Zeichen 1</a>',
            'url_second_part'   => 'Bitte ein gültiges Zeichen eingeben: <a href="#url_second_part">ID Zeichen 2</a>',
            'url_third_part'    => 'Bitte ein gültiges Zeichen eingeben: <a href="#url_third_part">ID Zeichen 3</a>',
            'url_fourth_part'    => 'Bitte ein gültiges Zeichen eingeben: <a href="#url_fourth_part">ID Zeichen 4</a>',
        ];

        // some of the data is invalid
        if ($invalid = invalid($data, $rules, $messages)) {
            $alert = $invalid;
        } else {

            $urlparticipant = $data['url_first_part'] . $data['url_second_part'] . $data['url_third_part'] . $data['url_fourth_part'];

            // everything is ok, handle information
            try {
                // Use condition to check the existence of URL
                if ($participantpage = $page->find($urlparticipant)) {
                    // if ($headers && strpos($headers[0], '200')) {
                    $alert[] = "Hallo " . $participantpage->title();
                    $pageexists = true;
                } else {
                    $pageexists = false;
                    $alert[] = "Der Teilnehmer existiert nicht.";
                }
            } catch (Exception $e) {
                $alert[] = 'Es ist ein Fehler aufgetreten ' . $e->getMessage();
            }
        }
    }

    # ----------- HANDLE SET PIN
    if ($kirby->request()->is('POST') && get('set-pin')) {

        $data = get();

        $urlparticipant = $data['participant_url'];
        $urlCharacters = str_split($urlparticipant);
        $pageexists = true;

        $rules = [
            'first_pin'     => ['integer', 'required'],
            'second_pin'    => ['integer', 'required'],
            'third_pin'     => ['integer', 'required'],
            'fourth_pin'    => ['integer', 'required'],
            'first_pin_confirm'     => ['integer', 'required'],
            'second_pin_confirm'    => ['integer', 'required'],
            'third_pin_confirm'     => ['integer', 'required'],
            'fourth_pin_confirm'    => ['integer', 'required'],
        ];

        $messages = [
            'first_pin'     => 'Bitte ein gültiges Zeichen eingeben: <a href="#first_pin">PIN Zeichen 1</a>',
            'second_pin'    => 'Bitte ein gültiges Zeichen eingeben: <a href="#second_pin">PIN Zeichen 2</a>',
            'third_pin'     => 'Bitte ein gültiges Zeichen eingeben: <a href="#third_pin">PIN Zeichen 3</a>',
            'fourth_pin'    => 'Bitte ein gültiges Zeichen eingeben: <a href="#fourth_pin">PIN Zeichen 4</a>',
            'first_pin_confirm'     => 'Bitte ein gültiges Zeichen eingeben: <a href="#first_pin_confirm">PIN Zeichen 1</a>',
            'second_pin_confirm'    => 'Bitte ein gültiges Zeichen eingeben: <a href="#second_pin_confirm">PIN Zeichen 2</a>',
            'third_pin_confirm'     => 'Bitte ein gültiges Zeichen eingeben: <a href="#third_pin_confirm">PIN Zeichen 3</a>',
            'fourth_pin_confirm'    => 'Bitte ein gültiges Zeichen eingeben: <a href="#fourth_pin_confirm">PIN Zeichen 4</a>',
        ];

        // some of the data is invalid
        if ($invalid = invalid($data, $rules, $messages)) {
            $alert = $invalid;
        } else {
            // authenticate as almighty
            //$kirby->impersonate('kirby');
            // everything is ok, handle information
            try {
                unset($data['set-pin']);

                $pin = $data['first_pin'] . $data['second_pin'] . $data['third_pin'] . $data['fourth_pin'];
                $pinConfirm = $data['first_pin_confirm'] . $data['second_pin_confirm'] . $data['third_pin_confirm'] . $data['fourth_pin_confirm'];

                if ($pin == $pinConfirm) {
                    $updatedCurator = $page->find($urlparticipant)->update([
                        'pin' => password_hash($pin,  PASSWORD_DEFAULT)
                    ]);

                    if ($updatedCurator) {
                        /* kirby()->session()->set([
                            'participantID' => $data['participant_url'],
                            'participantLogged'  => true
                        ]); */
                        $alert[] = "PIN gespeichert!";

                        $alert = $page->pinMe($page, $data);
                        //$target_page = $page->find($data['participant_url']);
                        //$target_page->go();
                    }
                } else {
                    $alert[] = "PIN stimmt nicht überein, bitte erneut versuchen.";
                }
            } catch (Exception $e) {
                $alert[] = 'Your registration failed: ' . $e->getMessage();
            }
        }

        $data['url_first_part'] = $urlCharacters[0];
        $data['url_second_part'] = $urlCharacters[1];
        $data['url_third_part'] = $urlCharacters[2];
        $data['url_fourth_part'] = $urlCharacters[3];
    }

    # ----------- HANDLE USER PAGE AUTH
    if ($kirby->request()->is('POST') && get('authenticate-participant')) {

        $data = get();

        $urlparticipant = $data['participant_url'];
        $urlCharacters = str_split($urlparticipant);
        $pageexists = true;

        $rules = [
            'first_pin'     => ['integer', 'required'],
            'second_pin'    => ['integer', 'required'],
            'third_pin'     => ['integer', 'required'],
            'fourth_pin'    => ['integer', 'required'],
        ];

        $messages = [
            'first_pin'     => 'Bitte ein gültiges Zeichen eingeben: <a href="#first_pin">PIN Zeichen 1</a>',
            'second_pin'    => 'Bitte ein gültiges Zeichen eingeben: <a href="#second_pin">PIN Zeichen 2</a>',
            'third_pin'     => 'Bitte ein gültiges Zeichen eingeben: <a href="#third_pin">PIN Zeichen 3</a>',
            'fourth_pin'    => 'Bitte ein gültiges Zeichen eingeben: <a href="#fourth_pin">PIN Zeichen 4</a>',
        ];

        // some of the data is invalid
        if ($invalid = invalid($data, $rules, $messages)) {
            $alert = $invalid;
        } else {
            // everything is ok, handle information
            $alert = $page->pinMe($page, $data);
        }

        $data['url_first_part'] = $urlCharacters[0];
        $data['url_second_part'] = $urlCharacters[1];
        $data['url_third_part'] = $urlCharacters[2];
        $data['url_fourth_part'] = $urlCharacters[3];
    }


    // return data to template
    return [
        'alert'         => $alert ?? null,
        'data'          => $data ?? false,
        'pageexists'    => $pageexists ?? false, // see if page exists
        'urlparticipant' => $urlparticipant ?? false, // send curator url to template to populate hidden field for next step
        'authenticated' => $site_vars['authenticated'] ?? false,
        'dataPage'      => $site_vars['dataPage'],
    ];
};
