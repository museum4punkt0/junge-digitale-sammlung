<?php

/**
 * Mainly handles the search and filtering ajax queries
 */
return function ($kirby, $page, $site) {

  // Grab the data from the default global site controller
  $site_vars = $kirby->controller('site.json', compact('kirby', 'page', 'site'));

  if ($kirby->request()->is('GET') && get('searchQuery')) { // a search was requested
    $searchQuery   = get('searchQuery');
    $searchResults = $site->search($searchQuery, 'title')->unlisted()->filterBy('intendedTemplate', get('currentCollection'))->filterBy('impulse', get('currentImpulse'));
  } else if ($kirby->request()->is('GET') && (get('currentCollection') || get('currentImpulse'))) { // otherwise it was a change of impulse (Thema) or collection (Objekt/Ausstellung)

    $params   = get();
    $items    = $site->children()->filterBy('intendedTemplate', 'c_workshop')
      ->children()->filterBy('intendedTemplate', $params['currentCollection'])
      ->filterBy('impulse', $params['currentImpulse'])
      ->sortBy('dateCreatedEpoch', 'desc');

    $limit    = intval($params['limit']);
    $offset   = intval($params['offset']);
    $type     = $params['currentCollection'];

    unset($params['offset']);
    unset($params['limit']);
    unset($params['currentImpulse']);
    unset($params['currentCollection']);
    unset($params['searchQuery']);
    unset($params['searchResults']);

    if ($type == 'c_exhibit') {
      // for each for params of users, like bundesland and klasse
      foreach ($params as $param => $value) {
        $items = $items->filter(function ($p) use ($param, $value) {
          $result = false;
          if ($u = $p->linked_user()->toPageOrDraft()) {
            $result = $u->content()->get($param) == $value;
          } 
          return $result;
        });
      }
    } elseif ($type == 'c_exhibition') {
      // for each for params of users, like bundesland and klasse
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
        });
      }
    }
    
    $more     = $items->count() > $offset + $limit;
    $items    = $items->offset($offset)->limit($limit);
    
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
