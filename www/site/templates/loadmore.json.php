<?php

foreach($loadmoreContent as $loadmoreElement) {

  $html .= snippet('factories/loadmore', ['project' => $loadmoreElement], true);

}
$json['html'] = $html;
$json['more'] = $more;

echo json_encode($json);