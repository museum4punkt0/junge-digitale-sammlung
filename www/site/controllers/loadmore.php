<?php

return function ($page, $kirby) {

  # Grab the data from the default controller for authentification
  $site_vars = $kirby->controller('site', compact('page', 'kirby'));
    
  $limit    = option('jds.loadmoresettings.blog', 12);
  $loadmoreContent = $page->children()->listed()->limit($limit);

  return [
      'limit'    => $limit,
      'loadmoreContent' => $loadmoreContent,    
      'authenticated' => $site_vars['authenticated'] ?? false,
      'dataPage' => $site_vars['dataPage'],
    ];
};