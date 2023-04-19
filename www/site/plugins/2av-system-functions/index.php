<?php

/**********
 * 
 * 
 * USER DATABASE
 * 
 * 
 **********/
function usernameExists($username)
{
    $workshops = site()->childrenAndDrafts()->filterBy('intendedTemplate', 'c_workshop');

    foreach ($workshops as $ws) {
        if ($ws->usernameExists($username))
            return true;
    }

    return false;
}

function usernameWrite($username, $oldUsername, $user, $wspage)
{
    $isChanging = $username != $oldUsername;

    if ($isChanging) {
        $workshops = site()->childrenAndDrafts()->filterBy('intendedTemplate', 'c_workshop');

        foreach ($workshops as $ws) {
            if ($ws == $wspage) {
                $ws->usernameWrite($username, $oldUsername, $user);
                return true;
            }
        }
    }

    return false;
}

function usernameRemove($username)
{
    $workshops = site()->childrenAndDrafts()->filterBy('intendedTemplate', 'c_workshop');

    foreach ($workshops as $ws) {
        if ($ws->usernameExists($username)) {
            $ws->usernameRemove($username);
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

function buildUsersTree($page, $amounts)
{
    $amounts = explode('&', $amounts);
    // get the subpage_builder definition from the blueprint
    $_title = "Teilnehmer";
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

    $_title = "Leiter";
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

function removeUnusedPages($ws)
{
    $succ = 0;

    if (!$ws->clean()->toBool()) {
        $filter = [/* 'c_curator', 'c_curator_leader', */'c_exhibit', 'c_exhibition']; // only remove exhibits and exhibitions
        $children = $ws->childrenAndDrafts()->filterBy('intendedTemplate', 'in', $filter);

        foreach ($children as $child) {
            if ($child->isDraft() && !$child->complete()->toBool()) { // is not complete and is still only draft

                if ($child->delete()) {
                    $succ++;
                    kirbylog($child->title()->value() . ' : was deleted');
                } else {
                    kirbylog($child->title()->value() . ' : ERROR deleting', 'error');
                }
                kirbylog('--');
            }
        }

        $wscleaned = $ws->update([
            'clean' => true
        ]);
    }

    return $succ;
}


function password_generate($chars)
{
    $data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz-!*$/';
    return substr(str_shuffle($data), 0, $chars);
}

function handleExhibitionMessages($data, $page)
{
    $forcedExhibitionValue = $data['impulse'] ?? null;
    $response['userMsg'] = '';
    $response['exhibitsMsg'] = '';
    $userWarnings = 0;
    $userErrors = 0;
    for ($x = 1; $x <= 5; $x++) {
        if (isset($data['user' . $x]) && $data['user' . $x] != '' && !empty($data['user' . $x])) {
            $result = matchImpulses($page, $data['user' . $x], $forcedExhibitionValue);
            if ($result  == 'result_error') {
                $userErrors++;
            } else if ($result == 'result_warning') {
                $userWarnings++;
            }
        }
    }
    if ($userWarnings != 0) {
        $response['userMsg'] .= $userWarnings . " verlinkte Teilnehmer haben noch keine Objekte";
    }
    if ($userErrors != 0) {
        $response['exhibitsMsg'] .= $userErrors . " verlinkte Objekte haben nicht passende Themen";
    }

    return $response;
}

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

/* check and set the 3D model for a preview image, in case the 3D file exists */
function bindImageTo3DModel($exhibitpage, $exhibit_preview_form_value = null)
{
    $exhibit_preview_form_value   = $exhibit_preview_form_value ?? ($exhibitpage->exhibit_preview()->isNotEmpty() ? str_replace(" ", "", $exhibitpage->exhibit_preview()->toFile()->uuid()->toString()) : null);

    if (isset($exhibit_preview_form_value)) {
        if (is_array($exhibit_preview_form_value) && isset($exhibit_preview_form_value[0]))
            $exhibit_preview_form_value = $exhibit_preview_form_value[0]['uuid'];
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

/* check and set the 3D models for all preview images in a workshop, in case the 3D files exists */
function bindAllImagesTo3DModels($workshop)
{
    kirbylog('----------NEW WS ASSET BINDING--------');
    kirbylog($workshop->title()->value());
    $newModelsFound = 0;
    $exhibitsUpdated = 0;
    $exhibits = $workshop->childrenAndDrafts()->filterBy('intendedTemplate', 'c_exhibit');
    foreach ($exhibits as $exhibit) {

        $modelValues = bindImageTo3DModel($exhibit);

        // update exhibit if new relevant values were found (model exists no more or model exists now)
        if ($modelValues) {
            // if new 3D models were found count up
            $nonEmptyValues = array_filter($modelValues ?? []);
            if ($nonEmptyValues && !empty($nonEmptyValues)) {
                $newModelsFound++;

                $exhibitsaved = $exhibit->update($modelValues);
                if ($exhibitsaved)
                    $exhibitsUpdated++;
            }
        }
    }

    return $newModelsFound;
}

/* create file from url */
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

/* empty images from page */
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

/* creates a compressed zip file */
function create_zip($page, $file)
{
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


/**
 * Gets data for embed url, based on Embed Class.
 * Also creates 'code' parameter for instagram, since
 * it doesnt have it out of the box.
 *
 * @param  string $url
 * @param  array $embed
 * @return array
 */
function scrapEmbed($url, $embed)
{
    try {
        $dispatcher = new Embed\Http\CurlDispatcher();
        $options = \Embed\Embed::$default_config;
        $options['min_image_width']         = option('sylvainjule.embed.min_image_width');
        $options['min_image_height']        = option('sylvainjule.embed.min_image_height');
        $options['html']['max_images']      = option('sylvainjule.embed.max_images');
        $options['html']['external_images'] = option('sylvainjule.embed.external_images');

        $media = Embed\Embed::create($url, $options, $dispatcher);

        if (strtolower($media->providerName) == 'instagram') {
            $media->code = snippet('renderers/embeds/instagram', ['embedurl' => $url], true);
        }

        $embed['status'] = 'success';
        $embed['data']   = array(
            'title'         => $media->title,
            'description'   => $media->description,
            'url'           => $media->url,
            'type'          => $media->type,
            'tags'          => $media->tags,
            'image'         => $media->image,
            'imageWidth'    => $media->imageWidth,
            'imageHeight'   => $media->imageHeight,
            'images'        => $media->images,
            'code'          => $media->code,
            'feeds'         => $media->feeds,
            'width'         => $media->width,
            'height'        => $media->height,
            'aspectRatio'   => $media->aspectRatio,
            'authorName'    => $media->authorName,
            'authorUrl'     => $media->authorUrl,
            'providerIcon'  => $media->providerIcon,
            'providerIcons' => $media->providerIcons,
            'providerName'  => $media->providerName,
            'providerUrl'   => $media->providerUrl,
            'publishedTime' => $media->publishedTime,
            'license'       => $media->license,
        );
    } catch (Exception $e) {
        $embed['status'] = 'error';
        $embed['error']  = $e->getMessage();
    }

    return $embed;
}
