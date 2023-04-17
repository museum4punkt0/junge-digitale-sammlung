<?php

return function ($kirby, $page, $site) {

  # Grab the data from the default controller for authentification
  $site_vars = $kirby->controller('site.json', compact('kirby', 'page', 'site'));

  if ($kirby->request()->is('GET') && get('exhibitionpage')) { // get data for updated exhibition

    $pageID = get('exhibitionpage');

    $exhibitionpage = $page->parent()->findPageOrDraft($pageID);
    $isCuratorLeader = true;

    if ($exhibitionpage) {
        //$exhibitionDataResult = $exhibitionpage->content()->toArray();
        $exhibitionDataResult = snippet('forms_blocks/fields/fields_exhibition', ['exhibition' => $exhibitionpage], true);
        //$exhibitionDataResult = snippet('forms_blocks/overlay-blocked', ['lockedBy' => '0000'], true);
    } else {
        //$exhibitionDataResult = 'UUID nicht gefunden.';
        $exhibitionDataResult = snippet('forms_blocks/fields/fields_exhibition', ['page' => $page], true);
    }
}

  return [
    'lockactionstatus' => $site_vars['lockactionstatus'],
    'lockedBy' => $site_vars['lockedBy'],
    'impulseResult' => $site_vars['impulseResult'],
    'overlayCode' => $site_vars['overlayCode'],
    'dataPage' => $site_vars['dataPage'],
    'embed' => $site_vars['embed'],
    
    'exhibitionDataResult'  => $exhibitionDataResult ?? null,
    'isCuratorLeader'  => $isCuratorLeader ?? null,
    
    'json' => [],
  ];
};
