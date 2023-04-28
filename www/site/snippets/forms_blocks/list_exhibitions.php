<?php
$index = 1;
foreach ($exhibitions as $exhibition) : ?>
    <div class="vstack border-bottom py-2 gap-3 exhibition-<?= $exhibition->slug() ?>">
        <form id="ef-<?= $exhibition->slug() ?>" class="exhibition-form hstack gap-3" action="<?= $page->url() ?>" method="POST">
            <div class="p-index meta help"><?= $index ?></div>
            <div class="p-title excerpt">
                <a href="<?= $exhibition->url() ?>" target="_blank"><?= $exhibition->title() ?></a>
            </div>
            <div class="o-count">
                <?php if ($exhibition->exhibitsMsg()->isNotEmpty()) : ?>
                    <span class="text-danger" data-bs-html="true" data-bs-custom-class="custom-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="<?= $exhibition->exhibitsMsg() ?>">
                        <?= $exhibition->getLinkedTotalExhibitsCount(); ?>
                    </span>
                <?php else : ?>
                    <?= $exhibition->getLinkedTotalExhibitsCount(); ?>
                <?php endif ?>
            </div>
            <div class="u-count">                
                <?php if ($exhibition->userMsg()->isNotEmpty() || $exhibition->userAmountMsg()->isNotEmpty()) : ?>
                    <?php $message = ($exhibition->userMsg() ?? '') . ($exhibition->userAmountMsg() ?? '') ?>
                    <span class="text-danger" data-bs-html="true" data-bs-custom-class="custom-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="<?= $message ?>">
                    <?= $exhibition->getLinkedUsersCount(); ?>
                    </span>
                <?php else : ?>
                    <?= $exhibition->getLinkedUsersCount(); ?>
                <?php endif ?>
            </div>
            <div class="p-status">
                <?php if ($exhibition->complete()->toBool()) : ?>
                    <span class="text-success" data-bs-html="true" data-bs-custom-class="custom-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Fertig">
                        <i icon-name="check-circle-2"></i>
                    </span>
                <?php else : ?>
                    <span class="text-warning" data-bs-html="true" data-bs-custom-class="custom-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="<?= e($exhibition->missingInfo()->isNotEmpty(), $exhibition->missingInfo(), 'Unvollständig') ?>">
                        <i icon-name="alert-circle"></i>
                    </span>
                <?php endif ?>
            </div>
            <div class="e-exhibition__edit d-grid">
                <button type="button" class="btn" data-pageid="<?= $exhibition->uuid() ?>" data-bs-toggle="modal" data-bs-target="#modal-name__<?= $exhibition->slug() ?>Modal">
                    <i icon-name="pencil" class="icon-only"></i>
                </button>
            </div>
            <div class="e-exhibition__delete d-grid">
                <button type="button" value=" " name="delete-exhibition" class="btn btn-exhibition-delete" data-bs-toggle="modal" data-bs-target="#confirmationModal" data-bs-headline="Ausstellung löschen: <?=$exhibition->title() ?>" data-bs-message="Sind Sie sicher, dass Sie die Ausstellung löschen wollen?" data-bs-func="sendAjaxForm" data-bs-data="ef-<?= $exhibition->slug() ?>">
                    <i icon-name="trash-2" class="icon-only"></i>
                </button>
                <input type="hidden" name="delete-exhibition" value="true" />
                <input type="hidden" name="exhibition-id" value="<?= $exhibition->uuid() ?>" />
            </div>
        </form>
    </div>
    <?= snippet('forms_blocks/modals/modal_exhibition', ['exhibition' => $exhibition]) ?>
    <?php $index++ ?>
<?php endforeach ?>