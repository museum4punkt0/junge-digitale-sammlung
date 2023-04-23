<?php

return function ($page, $kirby) {

  # Grab the data from the default global site controller
  $site_vars = $kirby->controller('site', compact('page', 'kirby'));
    
  // logic for loadmore
  $limit    = option('jds.loadmoresettings.blog', 12);

  return [
      'limit'    => $limit,
      'loadmoreContent' => [],    
      'authenticated' => $site_vars['authenticated'] ?? false,
      'dataPage' => $site_vars['dataPage'],
    ];
};