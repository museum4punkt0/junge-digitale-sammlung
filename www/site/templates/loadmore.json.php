<?php

foreach($loadmoreContent as $loadmoreElement) {

  $html .= snippet('factories/loadmore', ['element' => $loadmoreElement], true);

}
$json['html'] = $html;
$json['more'] = $more;

echo json_encode($json);