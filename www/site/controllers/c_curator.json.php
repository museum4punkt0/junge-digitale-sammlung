<?php

return function ($kirby, $page, $site) {

  // Grab the data from the default global site controller
  $site_vars = $kirby->controller('site.json', compact('kirby', 'page', 'site'));

  if ($kirby->request()->is('GET') && get('username')) { // for checking if username exists
    $username = get('username');

    if ($username) {
      $usernameExists = usernameExists($username);
    }
  }

  if ($kirby->request()->is('GET') && get('exhibitionpage')) { // get data for updated exhibition when we change tabs in the user area

    $pageID = get('exhibitionpage');

    $exhibitionpage = $page->parent()->findPageOrDraft($pageID);

    if ($page) {
      $exhibitionDataResult = snippet('forms_blocks/fields/fields_exhibition', ['exhibition' => $exhibitionpage, 'curator' => $page, 'dataPage' => $site_vars['dataPage']], true);
      $curatorInExhibition = $exhibitionpage->checkUser($page);
    } else {
      $exhibitionDataResult = 'UUID nicht gefunden.';
    }
  }

  # ------------ HANDLE DATA FOR PREVIEW & MODEL UPLOAD
  // model upload from the frontend has been deactivated on 22.03.2023, but the logic for save-model still remains here, just in case.
  // uploads are handled in JSON controller because we need a upload progress event in javascript
  if ($kirby->request()->is('POST') && (get('save-museum-preview') || get('save-preview') || get('save-asset') /* || get('save-model') */)) {

    //$exposure = 1;
    if (get('save-museum-preview')) {
      $upload = $kirby->request()->files()->get('museum_preview');
      $_file_template = "image";
      $_field = 'museum_preview';
    } else if (get('save-preview')) {
      $upload = $kirby->request()->files()->get('exhibit_preview');
      $_file_template = "image";
      $_field = 'exhibit_preview';
    } /* else if (get('save-model')) {
          $upload = $kirby->request()->files()->get('threed_model');
          $_file_template = "gltf";
          $_field = 'threed_model';

          $cleanName = pathinfo($upload['name'], PATHINFO_FILENAME);
          $exposure = explode('_%_exp=', $cleanName);

          if (sizeOf($exposure) > 1) {
              $exposure = $exposure[1];
          }
      } */ else if (get('save-asset')) {
      $upload = $kirby->request()->files()->get('digital_asset');
      $_file_template = "asset";
      $_field = 'digital_asset';
    }

    // check for duplicate
    $currentLinkedExhibit = $page->linked_exhibit()->toPageOrDraft();
    $files = $currentLinkedExhibit->files()->filterBy('template', $_file_template);

    $duplicates = $files->filter(function ($file) use ($upload) {
      return $file->filename() === F::safeName($upload['name']);
    });

    if ($duplicates->count() >= 1) {
      $alert[] = "Es gab " . $duplicates->count() . ' Duplikat. Datei wurde überschrieben.';
    }

    // we always remove all images, only one exists
    foreach ($files as $f) {
      $status = $f->delete();

      if (!$status)
        $alert[] = "Fehler beim Löschen einer alten Datei.";
    }
    kirbylog('---------------####');
    kirbylog($upload['name']);
    kirbylog($upload['tmp_name']);

    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
      $temppath = sys_get_temp_dir() . '\\' . time() . '-' . $upload['name'];
      $filecontent = F::read($upload['tmp_name']);
      F::write($temppath, $filecontent);
    } else {
      $temppath = $upload['tmp_name'];
    }

    try {
      $file = $currentLinkedExhibit->createFile([
        'source'   => $temppath,
        'filename' => $upload['name'],
        'template' => $_file_template,
        'content' => [
          'date' => date('Y-m-d h:m')
        ]
      ]);

      $updateexhibit = $currentLinkedExhibit->update([
        $_field => [$file->uuid()->toString()],
      ]);

      $alert[] = $file->filename();

      if (!$updateexhibit) {
        $alert[] = 'Leider ist ein Fehler bei der Aktualisierung des Objekts aufgetreten.';
      }

      $alert[] = 'Datei  wurde erfolgreich hochgeladen';
    } catch (Exception $e) {
      $alert[] = "ERROR: " . $e->getMessage();
    }

    F::remove($temppath);
  }

  return [
    'lockactionstatus' => $site_vars['lockactionstatus'],
    'lockedBy' => $site_vars['lockedBy'],
    'impulseResult' => $site_vars['impulseResult'],
    'overlayCode' => $site_vars['overlayCode'],
    'dataPage' => $site_vars['dataPage'],
    'embed' => $site_vars['embed'],

    'exhibitionDataResult'  => $exhibitionDataResult ?? null,
    'curatorInExhibition'  => $curatorInExhibition ?? null,

    'alert' => $alert ?? null,

    'usernameExists'  => $usernameExists ?? null,
    'json' => [],
  ];
};
