<?php

return function ($page) {

  $projects = $page->children()->listed();
  $limit    = 3;
  $offset   = intval(get('offset'));
  $more     = $projects->count() > $offset + $limit;
  $projects = $projects->offset($offset)->limit($limit);

  return [
      'loadmoreContent' => $projects,
      'more'     => $more,
      'html'     => '',
      'json'     => [],
    ];
};