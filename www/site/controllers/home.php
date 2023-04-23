<?php

return function ($page, $site, $kirby) {

  # Grab the data from the default global site controller
  $site_vars = $kirby->controller('site', compact('page', 'kirby', 'site'));

  // handles inital load more logic
  $limit    = option('jds.loadmoresettings.home', 12);
  $currentCollection = "c_exhibit";

  return [
    'limit'           => $limit,
    'currentCollection'  => $currentCollection,
    'loadmoreContent' => [],
    'authenticated' => $site_vars['authenticated'] ?? false,
    'dataPage' => $site_vars['dataPage'],
  ];
};
