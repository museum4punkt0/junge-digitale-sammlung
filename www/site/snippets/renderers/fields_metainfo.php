<?php
// we retrieve the dataPage here because these snippets get called via JSON as well
$dataPage = site()->data_fieldinfos_pick()->toPage();
if (isset($dataPage))
    $infoText = $dataPage->content()->get($name);
?>
<?php if (isset($composed) && isset($context)) : ?>
    <div class="metainfo__fields mb-4 pb-3">
        <?php
        $allFilesValid = true;
        foreach ($composed as $composedRule) {
            $allFilesValid = $allFilesValid && $context->content()->get($composedRule)->isNotEmpty();
        } ?>

        <?php if ($allFilesValid) : ?>
            <i icon-name="check-circle-2" class="text-success checks" data-for="<?= $name ?>"></i>
        <?php else : ?>
            <i icon-name="alert-circle" class="text-warning warnings" data-for="<?= $name ?>"></i>
        <?php endif ?>

        <?php if (isset($infoText) && $infoText && strlen($infoText) > 0) : ?>
            <span data-bs-html="true" data-bs-custom-class="custom-tooltip" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="<?= $infoText ?>">
                <i icon-name="info" class="text-primary text-opacity-50" data-for="<?= $name ?>"> </i>
            </span>
        <?php endif ?>
    </div>
<?php else : ?>
    <div class="metainfo__fields mb-4 pb-3">
        <i icon-name="check-circle-2" class="text-success checks d-none" data-for="<?= $name ?>"></i>
        <i icon-name="x-circle" class="text-danger errors d-none" data-for="<?= $name ?>"></i>
        <i icon-name="alert-circle" class="text-warning warnings d-none" data-for="<?= $name ?>"></i>

        <?php if (isset($infoText) && $infoText && strlen($infoText) > 0) : ?>
            <span data-bs-html="true" data-bs-custom-class="custom-tooltip" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="<?= $infoText ?>">
                <i icon-name="info" class="text-primary text-opacity-50" data-for="<?= $name ?>"> </i>
            </span>
        <?php endif ?>
    </div>
<?php endif ?>