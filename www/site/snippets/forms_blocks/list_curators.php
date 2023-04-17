<?php $exh = $participant->linked_exhibit()->toPageOrDraft() ?>
<div class="vstack border-bottom py-2 gap-3 participant-<?= $participant->slug() ?>">
    <form id="pf-<?= $participant->slug() ?>" class="user-form hstack gap-3" action="<?= $page->url() ?>" method="POST">
        <div class="p-index meta help"><?= $index ?></div>
        <div class="p-title excerpt">
            <?= $participant->title() ?>
        </div>
        <div class="p-id excerpt"><?= $participant->slug() ?></div>
        <div class="p-status">
            <?php if ($participant->complete()->toBool()) : ?>
                <span class="text-success" data-bs-html="true" data-bs-custom-class="custom-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Fertig">
                    <i icon-name="check-circle-2"></i>
                </span>
            <?php else : ?>
                <span class="text-warning" data-bs-html="true" data-bs-custom-class="custom-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="<?= e($participant->missingInfo()->isNotEmpty(), $participant->missingInfo(), 'Unvollständig') ?>">
                    <i icon-name="alert-circle"></i>
                </span>
            <?php endif ?>
        </div>
        <div class="o-status">
            <?php if ($exh) : ?>
                <?php if ($exh->complete()->toBool()) : ?>
                    <span class="text-success" data-bs-html="true" data-bs-custom-class="custom-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Fertig">
                        <i icon-name="check-circle-2"></i>
                    </span>
                <?php else : ?>
                    <span class="text-warning" data-bs-html="true" data-bs-custom-class="custom-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="<?= e($exh->missingInfo()->isNotEmpty(), $exh->missingInfo(), 'Unvollständig') ?>">
                        <i icon-name="alert-circle"></i>
                    </span>
                <?php endif ?>
            <?php else : ?>
                <span class="text-danger" data-bs-html="true" data-bs-custom-class="custom-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Objekt nicht vorhanden">
                    <i icon-name="x-circle"></i>
                </span>
            <?php endif ?>
        </div>
        <div class="o-link excerpt">
            <?php if ($exh) : ?>
                <a href="<?= $exh->url() ?>" target="_blank"><?= $exh->title() ?></a>
            <?php else : ?>
                -
            <?php endif ?>
        </div>
        <div class="p-pin__reset d-grid">
            <input type="hidden" name="reset-pin" value="true">
            <button type="button" value="reset-pin" <?php if ($participant->pin()->isEmpty()) : ?>disabled<?php endif ?> name="reset-pin" class="btn btn-primary btn-pin-reset" data-bs-toggle="modal" data-bs-target="#confirmationModal" data-bs-confirm-label="Bestätigen" data-bs-headline="PIN zurücksetzen" data-bs-message="Sind Sie sicher, dass Sie die PIN zurücksetzen wollen?" data-bs-func="sendAjaxForm" data-bs-data="pf-<?= $participant->slug() ?>">
                <i icon-name="history" class="icon-only"></i>
            </button>
            <input type="hidden" name="curator-id" value="<?= $participant->slug() ?>" />
        </div>
    </form>
</div>