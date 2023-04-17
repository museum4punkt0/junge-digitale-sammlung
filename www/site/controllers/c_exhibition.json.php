<?php

return function ($kirby, $page, $site) {

  // Grab the data from the default controller for authentification
  $site_vars = $kirby->controller('site.json', compact('kirby', 'page', 'site'));

  return [
    'embed' => $site_vars['embed'],
    'json'     => [],
  ];
};
