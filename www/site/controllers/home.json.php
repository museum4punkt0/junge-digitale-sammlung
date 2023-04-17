<?php

return function ($kirby, $page, $site) {

  // Grab the data from the default controller for authentification
  $site_vars = $kirby->controller('site.json', compact('kirby', 'page', 'site'));

  if ($kirby->request()->is('GET') && get('searchQuery')) { // Validation for impulse for curator list in leader

    $searchQuery   = get('searchQuery');
    //$searchResults = $site->search($searchQuery, 'title')->unlisted()->filterBy('intendedTemplate', get('currentCollection'));
    $searchResults = $site->search($searchQuery, 'title')->unlisted()->filterBy('intendedTemplate', get('currentCollection'));
  } else if ($kirby->request()->is('GET') && (get('currentCollection') || get('currentImpulse'))) {
    $params   = get();

    $items    = $site->children()->filterBy('intendedTemplate', 'c_workshop')
      ->children()->filterBy('intendedTemplate', $params['currentCollection'])
      ->filterBy('impulse', $params['currentImpulse'])
      ->sortBy('dateCreatedEpoch', 'desc');

    $limit    = intval($params['limit']);
    $offset   = intval($params['offset']);
    $type     = $params['currentCollection'];
    $more     = $items->count() > $offset + $limit;
    $items    = $items->offset($offset)->limit($limit);

    unset($params['offset']);
    unset($params['limit']);
    unset($params['currentImpulse']);
    unset($params['currentCollection']);
    unset($params['searchQuery']);
    unset($params['searchResults']);

    if ($type == 'c_exhibit') {
      // for each for params of users
      foreach ($params as $param => $value) {
        $items = $items->filter(function ($p) use ($param, $value) {
          return $p->linked_user()->toPageOrDraft()->content()->get($param) == $value;
        });
      }
    } elseif ($type == 'c_exhibition') {
      foreach ($params as $param => $value) {
        $items = $items->filter(function ($p) use ($param, $value) {
          if ($param == 'curator_state') {
            $states = $p->getLinkedUsersStates();
            return in_array($value, $states);
          }

          if ($param == 'schoolclass') {
            $classes = $p->getLinkedUsersClasses();
            return in_array($value, $classes);
          }
          //return $p->linked_user()->toPageOrDraft()->content()->get($param) == $value;
        });
      }
    }
  }

  return [
    'embed' => $site_vars['embed'],
    'loadmoreContent' => $items ?? null,
    'more'     => $more ?? null,
    'type'     => $type ?? null,
    'html'     => '',
    'comment'  => '',
    'searchQuery' => $searchQuery ?? null,
    'searchResults' => $searchResults ?? null,
    'json'     => [],
  ];
};
