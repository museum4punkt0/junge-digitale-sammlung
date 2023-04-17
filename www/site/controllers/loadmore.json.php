<?php

return function ($page) {

  // Grab the data from the default controller for authentification
  $site_vars = $kirby->controller('site.json', compact('kirby', 'page', 'site'));
  
  $projects = $page->children()->listed();
  $limit    = 3;
  $offset   = intval(get('offset'));
  $more     = $projects->count() > $offset + $limit;
  $projects = $projects->offset($offset)->limit($limit);

  return [
    'embed' => $site_vars['embed'],
    'loadmoreContent' => $projects,
    'more'     => $more,
    'html'     => '',
    'json'     => [],
  ];
};
