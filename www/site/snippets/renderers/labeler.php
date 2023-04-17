<?php
// we retrieve the dataPage here because these snippets get called via JSON as well
$dataPage = site()->data_fieldinfos_pick()->toPage();
if (isset($dataPage)) : ?>  
    <?= $dataPage->content()->get($field)->isNotEmpty() ? $dataPage->content()->get($field)->value() : $fallback ?>
<?php else : ?>
    <?= $fallback ?>
<?php endif ?>