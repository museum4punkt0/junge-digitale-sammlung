<?php

return function ($kirby, $page, $site) {

  // Grab the data from the default global site controller
  $site_vars = $kirby->controller('site.json', compact('kirby', 'page', 'site'));

  // logic for loadmore
  $subpages = $page->children()->listed();
  $limit    = intval(get('limit'));
  $offset   = intval(get('offset'));
  $more     = $subpages->count() > $offset + $limit;
  $subpages = $subpages->offset($offset)->limit($limit);

  return [
    'embed' => $site_vars['embed'],
    'loadmoreContent' => $subpages,
    'more'     => $more,
    'html'     => '',
    'json'     => [],
  ];
};
