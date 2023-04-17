<?php

$json['lockactionstatus'] = $lockactionstatus; 
$json['lockedBy'] = $lockedBy;
$json['impulseResult'] = $impulseResult; 
$json['overlayCode'] = $overlayCode; 
$json['exhibitionDataResult'] = $exhibitionDataResult; 
$json['curatorInExhibition'] = $curatorInExhibition; 
$json['dataPage'] = $dataPage; 

$json['alert'] = $alert;

$json['embed'] = $embed;

$json['usernameExists'] = $usernameExists;

echo json_encode($json);
