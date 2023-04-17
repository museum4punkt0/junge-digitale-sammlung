<?php if (isset($exhibition)) : ?>
    <div class="modal fade" data-pageid="<?= $exhibition->uuid() ?>" id="modal-name__<?= $exhibition->slug() ?>Modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="<?= $exhibition->slug() ?>ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <form id="exhibition-form-<?= $exhibition->slug() ?>" class="modal-content exhibition-form dynamic-content watchdog__form" action="<?= $page->url() ?>" method="POST">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="<?= $exhibition->slug() ?>ModalLabel"><?= $exhibition->title() ?></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <?= snippet('forms_blocks/fields/fields_exhibition', ['exhibition' => $exhibition]) ?>
                    </div>
                    <div class="overlay_container"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                    <input type="hidden" name="exhibition-id" id="exhibition-id" value="<?= $exhibition->slug() ?>">
                    <button class="btn btn-primary save__exhibition-button" type="submit" name="save-exhibition" value="Speichern">
                        Speichern <i icon-name="save" class="float-end"></i>
                    </button>
                </div>
            </form>

        </div>
    </div>
<?php else : ?>
    <div class="modal fade" id="exhibitionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exhibitionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <form id="exhibition-form" class="modal-content exhibition-form dynamic-content watchdog__form" action="<?= $page->url() ?>" method="POST">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exhibitionModalLabel">Neue Ausstellung</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <?= snippet('forms_blocks/fields/fields_exhibition') ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                    <button class="btn btn-primary create__exhibition-button" type="submit" name="create-exhibition" value="Anlegen">
                        Anlegen <i icon-name="save" class="float-end"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

<?php endif ?>