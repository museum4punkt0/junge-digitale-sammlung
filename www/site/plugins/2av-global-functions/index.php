<?php

/**********
 * 
 * 
 * USER DATABASE
 * 
 * 
 **********/


/**
 * usernameExists
 * Checks if username exists by checking each workshop's usernames table
 * @param  string $username
 * @return bool
 */
function usernameExists($username)
{
    $workshops = site()->childrenAndDrafts()->filterBy('intendedTemplate', 'c_workshop');

    foreach ($workshops as $ws) {
        if ($ws->usernameExistsInWorkshop($username))
            return true;
    }

    return false;
}

/**
 * usernameWrite
 *
 * @param  string $username
 * @param  string $oldUsername
 * @param  User $user
 * @param  Page $wspage
 * @return bool
 */
function usernameWrite($username, $oldUsername, $user, $wspage)
{
    $isChanging = $username != $oldUsername;

    if ($isChanging) {

        if ($wspage->usernameWriteInWorkshop($username, $oldUsername, $user)) // Model function! not this one
            return true;
        else
            return false;
    }

    return false;
}


/**
 * usernameRemove
 * Removes the user name from the Workshop that it contains it
 * @param  string $username
 * @return bool
 */
function usernameRemove($username)
{
    $workshops = site()->childrenAndDrafts()->filterBy('intendedTemplate', 'c_workshop');

    foreach ($workshops as $ws) {
        if ($ws->usernameExistsInWorkshop($username)) {
            $ws->usernameRemoveFromWorkshop($username);
            return true;
        }
    }

    return false;
}

/**********
 * 
 * 
 * Build sers tree
 * 
 * 
 **********/


/**
 * buildUsersTree
 * Creates the amount curators and curator_leaders that the admin has entered
 * @param  Page $page
 * @param  Array $amounts
 * @return void
 */
function buildUsersTree($page, $amounts)
{
    $amounts = explode('&', $amounts);
    // get the subpage_builder definition from the blueprint
    $_title = "Teilnehmer:in";
    $count = $amounts[0];
    for ($index = 0; $index < $count; $index++) {
        try {
            $subPage = $page->createChild([
                'content'  => [
                    'title' => $_title
                ],
                'slug'     => microtime() . $index,
                'template' => 'c_curator',
            ]);
        } catch (Exception $error) {
            throw new Exception($error);
        }
    }

    $_title = "Leiter:in";
    $count = $amounts[1];
    for ($index = 0; $index < $count; $index++) {
        try {
            $subPage = $page->createChild([
                'content'  => [
                    'title' => $_title
                ],
                'slug'     => microtime() . $index,
                'template' => 'c_curator_leader',
            ]);
        } catch (Exception $error) {
            throw new Exception($error);
        }
    }
}


/**
 * removeUnusedPages
 * Actual function for cleaning the workshops. Deletes
 * exhibitions and exhibits that are not complete and still a draft
 * @param  mixed $ws
 * @return void
 */
function removeUnusedPages($ws)
{
    $succ = 0;

    if (!$ws->clean()->toBool()) {
        $filter = [/* 'c_curator', 'c_curator_leader', */'c_exhibit', 'c_exhibition']; // only remove exhibits and exhibitions
        $children = $ws->childrenAndDrafts()->filterBy('intendedTemplate', 'in', $filter);

        foreach ($children as $child) {
            if ($child->isDraft() && !$child->complete()->toBool()) { // is not complete and is still only draft
                $prefix = " : " . $child->intendedTemplate()->name();
                if ($child->delete()) {
                    $succ++;
                    kirbylog($child->title()->value() . $prefix . ' : was deleted in ' . $ws->title());
                } else {
                    kirbylog($child->title()->value() . $prefix .  ' : ERROR deleting in ' . $ws->title(), 'error');
                }
                kirbylog('.');
            }
        }

        $wscleaned = $ws->update([
            'clean' => true
        ]);
    }

    return $succ;
}

/**
 * password_generate
 * Generates a password with the given lenght
 * @param  Number $chars
 * @return string
 */
function password_generate($chars)
{
    $data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz-!*$/';
    return substr(str_shuffle($data), 0, $chars);
}

/**
 * handleExhibitionMessages
 * Runs some checks to see what information is missing is mismatching inside a workshop
 * @param  array $data
 * @param  Page $page
 * @return array
 */
function handleExhibitionMessages($data, $page)
{
    $forcedExhibitionValue = $data['impulse'] ?? null;
    $response['userMsg'] = '';
    $response['exhibitsMsg'] = '';
    $response['userAmountMsg'] = '';
    $userWarnings = 0;
    $userErrors = 0;
    $userAmount = 0;
    for ($x = 1; $x <= 5; $x++) {
        if (isset($data['user' . $x]) && $data['user' . $x] != '' && !empty($data['user' . $x])) {
            $userAmount++;
            $result = matchImpulses($page, $data['user' . $x], $forcedExhibitionValue);
            if ($result  == 'result_error') {
                $userErrors++;
            } else if ($result == 'result_warning') {
                $userWarnings++;
            }
        }
    }
    if ($userWarnings != 0) {
        $response['userMsg'] .= $userWarnings . " verlinkte Teilnehmer haben noch keine Objekte. ";
    }
    if ($userErrors != 0) {
        $response['exhibitsMsg'] .= $userErrors . " verlinkte Objekte haben nicht passende Themen. ";
    }
    if ($userAmount < 3) {
        $response['userAmountMsg'] .= "Bitte mindestens 3 Teilnehmer auswählen. ";
    }

    return $response;
}

/**
 * matchImpulses
 * Checks if impulse (Thema) of an exhibition matches the exhibit impulse of a participant
 * @param  Page $exhibition
 * @param  mixed $curatorID
 * @param  string $forcedExhibitionValue
 * @return void
 */
function matchImpulses($exhibition, $curatorID, $forcedExhibitionValue = null)
{
    if (isset($forcedExhibitionValue)) {
        $exhibitionImpulse = $forcedExhibitionValue;
    } else {
        $exhibitionImpulse = $exhibition->impulse()->value();
    }

    // in case the ID was passed as String from UUID '- page://xxxxxxxxxxxxx'    
    if (is_string($curatorID)) {
        $curatorID =  str_replace('- ', '', $curatorID);
    } else {
        $curatorID =  $curatorID[0]['uuid'];
    }

    if ($exhibition) {
        if ($user = $exhibition->parent()->findPageOrDraft($curatorID) ?? $exhibition->parent()->find($curatorID)) {
            //if ($user = $exhibition->parent()->findPageOrDraft($curatorID)) {
            if ($exhibit = $user->linked_exhibit()->toPageOrDraft()) {
                // exhibit found, check if impulses match
                if ($exhibit->impulse()->isNotEmpty()) {
                    $exhibitImpulse = $exhibit->impulse()->value();
                    if ($exhibitionImpulse == $exhibitImpulse) {
                        $impulseResult = 'result_success';
                    } else {
                        $impulseResult = 'result_error';
                    }
                } else {
                    $impulseResult = 'Objekt hat kein Thema';
                    $impulseResult = 'result_error';
                }
            }
            // Objekt nicht gefunden oder vorhanden 
            else {
                $impulseResult = 'Objekt nicht gefunden oder vorhanden';
                $impulseResult = 'result_warning';
            }
        }
        // UUID not found, user not found
        else {
            $impulseResult = 'UUID Teilnehmer nicht gefunden';
        }
    }
    // UUID not found, no object found
    else {
        $impulseResult = 'UUID Ausstellung nicht gefunden';
    }

    return $impulseResult;
}


/**
 * populateAge
 * Populates the numeric age dropdown by returning a Structure containing the numbers needed
 * @param  int $start
 * @param  int $end
 * @param  bool $invert
 * @return Structure
 */
function populateAge(int $start = 1, int $end = 99, $invert = false)
{
    $_structure = new Kirby\Cms\Structure();

    $dataPage = site()->data_populators_pick()->toPage();
    $extraItems = [];

    if ($dataPage) {
        if ($dataPage->age_start()->isNotEmpty())
            $start = $dataPage->age_start()->value();

        if ($dataPage->age_end()->isNotEmpty())
            $end = $dataPage->age_end()->value();

        $extraItems = $dataPage->age_structure()->toStructure();
    }

    for ($i = 0; $i <= ($end - $start); $i++) {
        $_structure->add(new Kirby\Cms\StructureObject([
            'id'        => strval($start + $i),
            'content'   => ['desc' => strval($start + $i)]
        ]));
    }

    foreach ($extraItems as $structureItem) {
        $_structure->add(new Kirby\Cms\StructureObject([
            'id'        => $structureItem->desc()->slug(),
            'content'   => ['desc' => $structureItem->desc()]
        ]));
    }

    if ($invert)
        $_structure = $_structure->flip();

    return $_structure;
}

/**
 * populateFromCollection
 * Populates a dropdown for the existing participants in a workshop in the front end
 * (Workshop-Area). Sets a flag, if the participant is already part of an exhibition.
 * @param  Collection $collection
 * @return Structure
 */
function populateFromCollection($collection)
{
    $_structure = new Kirby\Cms\Structure();

    foreach ($collection as $item) {

        if ($item->linked_exhibit() && $item->linked_exhibit()->isNotEmpty())
            $extraText = $item->linked_exhibit()->toPageOrDraft()->title();
        else
            $extraText = 'Kein Objekt';

        $_structure->add(new Kirby\Cms\StructureObject([
            'id'        => $item->id(),
            'content'   => [
                'desc' => $item->title() . ' / ' . $extraText,
                'hasExhibitionAlready' => $item->linked_exhibition()->toPageOrDraft() ? $item->linked_exhibition()->toPageOrDraft()->title() : false
            ],
        ]));
    }

    return $_structure;
}

/**
 * random_strings
 * Creates some random strings according to the lenght and digits given.
 * Used for curator and curator leader IDs
 * @param  mixed $length_of_string
 * @param  mixed $digitBlock
 * @return void
 */
function random_strings($length_of_string, $digitBlock = 1)
{
    // String of all alphanumeric character
    $str_result = '0123456789abcdefghijklmnopqrstuvwxyz';
    $result = '';
    while ($digitBlock > 0) {
        $result .= substr(
            str_shuffle($str_result),
            0,
            $length_of_string
        );
        $digitBlock--;
    }

    // Shuffle the $str_result and returns substring
    // of specified length
    return $result;
}


/**
 * bindImageTo3DModel
 * Check and set the 3D model for a preview image, in case the 3D file exists
 * @param  Page $exhibitpage
 * @param  mixed $exhibit_preview_form_value
 * @return array | bool
 */
function bindImageTo3DModel($exhibitpage, $exhibit_preview_form_value = null)
{
    $exhibit_preview_form_value   = $exhibit_preview_form_value ?? ($exhibitpage->exhibit_preview()->isNotEmpty() ? str_replace(" ", "", $exhibitpage->exhibit_preview()->toFile()->uuid()->toString()) : null);

    if (isset($exhibit_preview_form_value)) {
        if (is_array($exhibit_preview_form_value) && isset($exhibit_preview_form_value[0])) {
            $exhibit_preview_form_value = $exhibit_preview_form_value[0]['uuid'];
        }

        $previewimg = $exhibitpage->parent()->files()->findBy("uuid", $exhibit_preview_form_value);
        if ($previewimg) {
            kirbylog($previewimg->filename() . ' preview image found');
            $threedModel = $previewimg->findModelFromImage();
            if ($threedModel) {
                kirbylog($threedModel->filename() . ' model found');

                $current3Dmodell = $exhibitpage->threed_model()->toFile();
                if (!$current3Dmodell || $current3Dmodell->uuid() != $threedModel->uuid()) {
                    $input['threed_model_light'] = $threedModel->getExposure();
                    $input['threed_model'] = [$threedModel->uuid()];
                    kirbylog('model UPDATED');
                } else {
                    kirbylog('model was the same');
                }
            } else {
                kirbylog('model NOT found');
                $input['threed_model'] = [];
            }

            if ($previewimg->uuid()->value() != $exhibit_preview_form_value) {
                $input['exhibit_preview'] = [$previewimg->uuid()];
            }
        } else {
            kirbylog('preview image uuid NOT found in workshop');
            $input['threed_model'] = [];
        }
    } else {
        kirbylog('preview image data NOT found (exhibit_preview_form_value)');
    }

    kirbylog('--');

    return $input ?? false;
}


/**
 * bindAllImagesTo3DModels
 * Check and set the 3D models for all preview images
 * inside a workshop, in case the 3D files exists. Gets
 * called via Janitor Button Plugin in Admin area
 * @param  Page $workshop
 * @return int
 */
function bindAllImagesTo3DModels($workshop)
{
    kirbylog('----------NEW WS ASSET BINDING--------');
    kirbylog($workshop->title()->value());
    $newModelsFound = 0;
    $exhibitsUpdated = 0;
    $exhibits = $workshop->childrenAndDrafts()->filterBy('intendedTemplate', 'c_exhibit');
    foreach ($exhibits as $exhibit) {

        if ($exhibit->type()->value() == '0') { // type 0 for physical object
            kirbylog('physical object ' . $exhibit->title());
            $modelValues = bindImageTo3DModel($exhibit);
            // update exhibit if new relevant values were found (model exists no more or model exists now)
            if ($modelValues) {
                // if new 3D models were found count up
                unset($modelValues['exhibit_preview']);
                $nonEmptyValues = array_filter($modelValues ?? []);
                if ($nonEmptyValues && !empty($nonEmptyValues)) {
                    $newModelsFound++;
                    $modelValues['skipRechecking3D'] = true;
                    $exhibitsaved = $exhibit->update($modelValues);
                    if ($exhibitsaved)
                        $exhibitsUpdated++;
                }
            }
        }
    }

    return $newModelsFound;
}

/**
 * makeImage
 * Creates a Kirby file from a url
 * @param  Page $exhibit
 * @param  string $external_url
 * @return File
 */
function makeImage($exhibit, $external_url)
{
    if (!$external_url)
        return;

    $imageName = F::safeName('social_preview-' . $exhibit->title());

    $imageData = file_get_contents($external_url);
    file_put_contents($exhibit->root() . DS . $imageName . '.jpg', $imageData);

    $image = new File([
        'parent'   => $exhibit,
        'filename' => $imageName . '.jpg',
        'template' => 'image',
    ]);

    $image->update();

    return $image;
}

/**
 * emptyImages
 * Empties images from page with template 'image'
 * @param  Page $exhibit
 * @return bool
 */
function emptyImages($exhibit)
{
    $status = true;

    if ($exhibit->hasFiles()) {
        $files = $exhibit->files()->filterBy('template', 'image');

        // we always remove all images, only one must exist
        foreach ($files as $f) {
            $status = $status && $f->delete();
        }
    }

    return $status;
}

/**
 * create_zip
 * Creates a compressed zip file. Relevant for materials
 * @param  mixed $page
 * @param  mixed $file
 * @return void
 */
function create_zip($page, $file)
{
    if (!$page) //in case its site
        return;
    if ($page->intendedTemplate()->name() == 'material') {
        $pagefiles = $page->files()->filter(function ($f) {
            return $f->extension() != 'zip';
        });
        $filename = 'jds_material-' . $page->uid() . '.zip';
        $tempfilename = 'jds_material-' . $page->uid() . '-temp.zip';
        $zip = new ZipArchive();

        $content_folder = $page->contentFileDirectory();

        $zipfile = $content_folder . '/' . $tempfilename;

        if ($pagefiles->count() >= 1) {

            $zip->open($zipfile, ZipArchive::CREATE | ZIPARCHIVE::OVERWRITE === TRUE);

            foreach ($pagefiles as $pagefile) {
                if ($pagefile->extension() != 'zip') {
                    $zip->addFile($pagefile->root(), basename($pagefile->filename()));
                }
            }

            $zip->close();

            if ($oldfile = $page->files()->find($filename)) {
                $oldfile->replace($zipfile);
            } else {
                $page->createFile([
                    'source'   => $zipfile,
                    'filename' => $filename,
                    'parent' => $page,
                    'template' => 'files',
                ]);
            }

            try {
                unlink($zipfile);
            } catch (Exception $e) {
            }
        } else {
            if ($oldfile = $page->files()->find($filename)) {
                $oldfile->delete();
            }
        }
    }
}
