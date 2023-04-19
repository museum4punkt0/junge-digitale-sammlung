<?php

return function ($page, $site, $kirby) {

  # Grab the data from the default controller for authentification
  $site_vars = $kirby->controller('site', compact('page', 'kirby'));

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
