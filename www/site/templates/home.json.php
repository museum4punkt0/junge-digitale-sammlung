<?php

if (isset($loadmoreContent)) {
  foreach ($loadmoreContent as $loadmoreElement) {
    $html .= snippet('factories/loadmore-home', ['collectionItem' => $loadmoreElement, 'type' => $type], true);
  }
  $json['html'] = $html;
  $json['more'] = $more;
}

if (isset($searchQuery)) {
  $json['searchQuery'] = $searchQuery;

  $resultPages = [];
  foreach ($searchResults as $id => $sr) {


    array_push($resultPages, [
      'title' => $sr->title()->value(),
      'url'   => $sr->id(),
      'type'  => $sr->intendedTemplate(),
    ]);

    //$resultPages .= snippet('factories/search-results', ['title' => $sr->title(), 'url' => $sr->id(), 'type' => $sr->intendedTemplate()], true);
  }

  $json['searchResults'] = $resultPages;
}

$json['comment'] = $comment;
$json['embed'] = $embed;

echo json_encode($json);
