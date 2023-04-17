<?php

$json['lockactionstatus'] = $lockactionstatus; 
$json['lockedBy'] = $lockedBy;
$json['impulseResult'] = $impulseResult; 
$json['overlayCode'] = $overlayCode; 
$json['exhibitionDataResult'] = $exhibitionDataResult;
$json['isCuratorLeader'] = $isCuratorLeader; 
$json['dataPage'] = $dataPage; 

echo json_encode($json);
