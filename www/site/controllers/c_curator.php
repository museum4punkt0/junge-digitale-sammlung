<?php

/*** 
 * TODO
 * populate after form submit still unclean because of $data['title']
 */
return function ($kirby, $page, $site) {

    # Grab the data from the default controller for authentification
    $site_vars = $kirby->controller('site', compact('page', 'kirby'));

    if (!$site_vars['authenticated']) {
        go($kirby->url() . '/login');
    }

    # ----------- HANDLE DATA FOR USER
    if ($kirby->request()->is('POST') && get('save-user')) {

        $data = get();
        $username = $kirby->site()->username_db()->yaml();
        $isChanging = $data['username'] != $page->username();

        $formRules = [
            'fullname'              => ['required'],
            'username'              => ['required'],
        ];

        $messages = [
            'fullname'  => 'Bitte einen Name eingeben.',
            'username'  =>  'Bitte einen Benutzername eingeben.',
        ];

        // some of the data is invalid
        if ($invalid = invalid($data, $formRules, $messages)) {
            $alert = $invalid;
        } else {
            if ($isChanging && usernameExists($data['username'])) {
                $alert[] = 'Benutzername existiert bereits.';
                $metainfo = "user-error";
            } else {
                // everything is ok, handle information
                try {
                    $data['title'] = $data['fullname']; // workaround because page and object have same attribute 'title'. we also save 'fullname' for message functionality
                    unset($data['save-user']);

                    $updatedCurator = $page->update($data);

                    if ($updatedCurator) {
                        $alert[] = 'Benutzer erfolgreich gespeichert!';

                        unset($data['title']);
                    }

                    $updateSite = usernameWrite($data['username'], $page->username(), $page->id(), $page->parent());

                    if ($updateSite) {
                        //$alert[] = 'DB saved';
                    }
                } catch (Exception $e) {
                    $alert[] = 'Es ist ein Fehler aufgetretten: ' . $e->getMessage();
                }
            }
        }
    }


    # ----------- HANDLE CREATE EXHIBIT
    if ($kirby->request()->is('POST') && get('create-exhibit')) {
        $data = get();
        $data['modifiedByUser'] = $kirby->user()->name();
        $data['tempslug'] = $data['exhibitname'];
        $data['linked_user'] = [$page];
        $data['from_frontend'] = true;

        $formRules = [
            'exhibitname'  => ['required'],
            'type'   => ['required'],
        ];

        $messages = [
            'exhibitname'  => 'Bitte einen Objektname eingeben.',
            'type'  => 'Bitte einen Objekttyp eingeben.',
        ];

        // some of the data is invalid
        if ($invalid = invalid($data, $formRules, $messages)) {
            $alert = $invalid;
        } else {
            try {
                $data['title'] = $data['exhibitname'];
                unset($data['create-exhibit']);
                unset($data['exhibitname']);
                // create exhibit
                $exhibit = $page->parent()->createChild([
                    'slug'     => time(),
                    'template' => 'c_exhibit',
                    'content'  => $data
                ]);

                if ($exhibit) {
                    $alert[] = 'Objekt erfolgreich angelegt!';
                    $data['exhibitname'] = $data['title'];
                    unset($data['title']);
                    // update linkage of curator happens in hook changeSlug:after of exhibit

                    /* // refresh message infos in exhibition, if it is linked
                    if ($exhibition = $page->linked_exhibition()->toPageOrDraft()) {
                        $exhibition_msgs = handleExhibitionMessages($exhibition->content()->data(), $exhibition);
                        $exhibition->update($exhibition_msgs);
                    } */
                }
            } catch (Exception $e) {
                $alert[] = 'Objekt-Registrierung fehlgeschlagen: ' . $e->getMessage();
            }
        }
    }

    # ----------- HANDLE DELETE EXHIBIT
    if ($kirby->request()->is('POST') && get('delete-exhibit')) {
        $data = get();

        try {
            // delete exhibit

            // check if mabye already deleted and show message
            if ($exhibit = $page->linked_exhibit()->toPageOrDraft()) {
                $success = $exhibit->delete();
            } else {
                $alert[] = 'Objekt erfolgreich gelöscht!';
            }


            if (isset($success) && $success) {
                $alert[] = 'Objekt erfolgreich gelöscht!';
            }
        } catch (Exception $e) {
            $alert[] = 'Löschen Fehlgeschlagen: ' . $e->getMessage();
        }
    }

    # ----------- HANDLE DATA FOR EXHIBIT
    if ($kirby->request()->is('POST') && get('save-exhibit')) {

        $data = get();
        $data['modifiedByUser'] = $kirby->user()->name();

        $formRules = [];
        $messages = [];

        $currentLinkedExhibit = $page->linked_exhibit()->toPageOrDraft();
        $exhibitType = $currentLinkedExhibit->type();

        $exhibitImpulse = $data['impulse'] ?? $currentLinkedExhibit->impulse()->value();
        $linkedExhibition = $page->linked_exhibition()->toPageOrDraft();

        if ($linkedExhibition && $linkedExhibition->impulse()->value() != $exhibitImpulse) {
            $alert[] = 'Achtung: das Thema passt nicht zum Thema der Ausstellung!';
        }

        // for scrapping embed content and saving the meta info
        if ($exhibitType == '1') {
            $embed = [];
            $url = get('embed_url');
            $oldUrl = $currentLinkedExhibit->embed_url()->isNotEmpty() ? $currentLinkedExhibit->embed_url()->yaml() : array('input' => '');

            if (!isset($oldUrl['input']) || $url != $oldUrl['input']) {
                emptyImages($currentLinkedExhibit);
                if (!V::url($url)) {
                    $embed['status'] = 'error';
                    $embed['error']  = 'The $url variable is not an url';
                } else {
                    $embed = $site->getEmbedData($url);

                    if (isset($embed['data']) && $embed['data']) {
                        $data['embed_url'] = [
                            'input' => $url,
                            'media' => $embed['data'],
                        ];

                    } else {
                        $data['embed_url'] = [
                            'input' => $url,
                            'media' => '',
                        ];
                    }
                }
            } else {
                $data['embed_url'] = $currentLinkedExhibit->embed_url()->yaml() ?? null;
            }
        }

        // some of the data is invalid
        if ($invalid = invalid($data, $formRules, $messages)) {
            $alert = $invalid;
        } else {
            // everything is ok, handle information
            try {
                $data['title'] = $data['exhibitname']; // workaround because page and object have same attribute 'title'

                unset($data['exhibitname']);
                unset($data['save-exhibit']);

                $changeSlug = $currentLinkedExhibit->title() != $data['title'];
                $updateexhibit = $currentLinkedExhibit->update($data);

                if ($updateexhibit) {
                    // store referer and name in session
                    $kirby->session()->set([
                        'referer' => $page->uri(),
                        'regName'  => esc($page->title()),
                        'uname' => $kirby->user()->name()
                    ]);

                    if ($changeSlug) {                        
                        $indexedSlug = $updateexhibit->checkSlugIndex(Str::slug($data['title']));
                        $updateexhibit->changeSlugOnly($indexedSlug);
                    }

                    $data['exhibitname'] = $data['title'];
                    unset($data['title']);

                    $alert[] = 'Objekt erfolgreich gespeichert!';
                }
            } catch (Exception $e) {
                $alert[] = 'Es ist ein Fehler aufgetretten: ' . $e->getMessage();
            }
        }
    }

    # ------------ HANDLE DELETE FOR PREVIEW & MODEL & ASSET
    if ($kirby->request()->is('POST') && (get('delete-museum-preview') || get('delete-preview') || get('delete-model') || get('delete-asset'))) {

        if (get('delete-museum-preview')) {
            $_file_template = "image";
            $_field = 'museum_preview';
        } elseif (get('delete-preview')) {
            $_file_template = "image";
            $_field = 'exhibit_preview';
        } elseif (get('delete-model')) {
            $_file_template = "gltf";
            $_field = 'threed_model';
        } elseif (get('delete-asset')) {
            $_file_template = "asset";
            $_field = 'digital_asset';
        }

        // check for duplicate
        $currentLinkedExhibit = $page->linked_exhibit()->toPageOrDraft();
        $files = $currentLinkedExhibit->files()->filterBy('template', $_file_template);
        // we always remove all images, only one exists
        foreach ($files as $f) {
            $status = $f->delete();

            if (!$status)
                $alert[] = "Fehler beim Löschen einer alten Datei.";
        }

        try {
            $updateexhibit = $currentLinkedExhibit->update([
                $_field => [''],
            ]);

            if (!$updateexhibit) {
                $alert[] = 'Leider ist ein Fehler beim Speichern des Objekts aufgetretten.';
            }

            $alert[] = 'Datei wurde erfolgreich gelöscht';
        } catch (Exception $e) {
            $alert[] = "ERROR: " . $e->getMessage();
        }
    }

    # ----------- HANDLE EDIT EXHIBITION
    if ($kirby->request()->is('POST') && get('save-exhibition')) {

        $data = get();
        $data['modifiedByUser'] = $kirby->user()->name();

        $currentExhibition = $page->parent()->childrenAndDrafts()->find($data['exhibition-id']);
        $data['impulse'] = $currentExhibition->impulse()->value(); // because field is disabled for curator, so we read already saved value by curator_leader

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

                    if ($changeSlug) {
                        $indexedSlug = $updateexhibition->checkSlugIndex(Str::slug($data['title']));
                        $updateexhibition->changeSlugOnly($indexedSlug);
                    }

                    $alert[] = 'Ausstellung erfolgreich gespeichert!';

                    unset($data['title']);
                }
            } catch (Exception $e) {
                $alert[] = 'Es ist ein Fehler aufgetretten: ' . $e->getMessage();
            }
        }
    }

    // logout in case it was auto-logout
    if ($kirby->request()->is('POST') && get('logout-after-save')) {
        go($kirby->url() . '/logout');
    }

    // return data to template
    return [
        'alert'         => $alert ?? null,
        'data'          => $data ?? false,
        'metainfo'      => $metainfo ?? null,
        'authenticated' => $site_vars['authenticated'] ?? false,
        'dataPage'      => $site_vars['dataPage'],
    ];
};
