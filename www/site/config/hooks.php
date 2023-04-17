<?php

use claviska\SimpleImage;

// HOOKS
return [

    /********* USER **********/
    'user.logout:before' => function (Kirby\Cms\User $user, Kirby\Session\Session $session) {
        // your code goes here
    },

    'user.update:after' => function (Kirby\Cms\User $newUser, Kirby\Cms\User $oldUser) {
        /*** REBUILD USER and WS REFERENCES ****/
        if ($newUser->role() == "frontenduser") {
            $linked_workshopNew = $newUser->linked_workshop()->toPageOrDraft();
            $linked_workshopOld = $oldUser->linked_workshop()->toPageOrDraft();

            if ($linked_workshopNew != $linked_workshopOld) {

                if ($linked_workshopNew && !$linked_workshopOld) {
                    // from none to selected
                    if ($oldUserNewWorkshop = $linked_workshopNew->mainuser()->toUser()) { // reset olduser from new workshop  

                        if ($oldUserNewWorkshop->id() != $newUser->id()) {
                            $result = $oldUserNewWorkshop->update([
                                'linked_workshop' => []
                            ]);
                        }
                    }
                    $linked_workshopNew->update([
                        'mainuser' => [$newUser]
                    ]);
                } else if (!$linked_workshopNew && $linked_workshopOld) {
                    // from selected to none

                    $linked_workshopOld->update([
                        'mainuser' => []
                    ]);
                } else {
                    // changed users

                    if ($oldUserNewWorkshop = $linked_workshopNew->mainuser()->toUser()) { // reset olduser from new workshop                        
                        $result = $oldUserNewWorkshop->update([
                            'linked_workshop' => []
                        ]);
                    }

                    $linked_workshopNew->update([ // set newuser in new workshop
                        'mainuser' => [$newUser]
                    ]);

                    $linked_workshopOld->update([ // reset me from old workshop
                        'mainuser' => []
                    ]);
                }
            }
        }
    },

    /********* PAGE **********/
    'page.update:before' => function ($page, $values, $strings) {
        $modelName = a::get(Page::$models, $page->intendedTemplate()->name());

        if ($modelName && method_exists($modelName, 'hookPageUpdateBefore')) {
            $modelName::hookPageUpdateBefore($page, $values, $strings);
        }
    },

    'page.update:after' => function ($newPage, $oldPage) {
        $modelName = a::get(Page::$models, $newPage->intendedTemplate()->name());

        if ($modelName && method_exists($modelName, 'hookPageUpdateAfter')) {
            $modelName::hookPageUpdateAfter($newPage, $oldPage);
        }
    },

    'page.changeSlug:after' => function ($newPage, $oldPage) {
        $modelName = a::get(Page::$models, $newPage->intendedTemplate()->name());

        if ($modelName && method_exists($modelName, 'hookChangeSlugAfter')) {
            $modelName::hookChangeSlugAfter($newPage, $oldPage);
        }
    },

    'page.delete:before' => function ($page, $force) {
        $modelName = a::get(Page::$models, $page->intendedTemplate()->name());

        if ($modelName && method_exists($modelName, 'hookDeleteBefore')) {
            $modelName::hookDeleteBefore($page, $force);
        }
    },
    'page.delete:after' => function ($status, $page) {
        $modelName = a::get(Page::$models, $page->intendedTemplate()->name());

        if ($modelName && method_exists($modelName, 'hookDeleteAfter')) {
            $modelName::hookDeleteAfter($status, $page);
        }
    },


    /********* FILE **********/
    'file.create:after' => function ($file) {
        $page = $file->page();
        create_zip($page, $file);

        if ($file->type() == "image") {
            // workaround for iphone images that have wrong orientation
            $orientation = $file->exif()->data()['Orientation'] ?? 1;
            if ($orientation !== 1) {
                (new SimpleImage)
                    ->fromFile($file->root())
                    ->autoOrient()
                    ->toFile($file->root());
            }
        }
    },
    'file.delete:after' => function ($status, $file) {
        $page = $file->page();
        create_zip($page, $file);
    },
    'file.changeName:after' => function ($newFile, $oldFile) {
        $page = $newFile->page();
        create_zip($page, $newFile);
    },
    'file.replace:after' => function ($newFile, $oldFile) {
        $page = $newFile->page();
        create_zip($page, $newFile);
    },
];
