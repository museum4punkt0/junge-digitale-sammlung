<!-- image upload -->
<div class="preview__area w-50 form__dnd position-relative">
    <form class="upload-form" action="<?= $page->url() ?>" enctype="multipart/form-data" method="POST">
        <label for="exhibit_preview">
            <?= snippet('renderers/labeler', ['field' => 'exhibit_preview_label', 'fallback' => 'Vorschaubild / Bild']) ?>
        </label>
        <?= snippet('renderers/input_element', ['_page' => $linked_exhibit, 'name' => 'exhibit_preview', 'extraClasses' => 'files', /* 'value' => $page->exhibit_preview(), */ 'type' => 'dnd', 'accept' => '.jpg, .png, .heic, .jpeg']); ?>
        <button type="submit" name="save-preview" value="Vorschaubild aktualisieren" class="btn btn-primary d-none">
            <i icon-name="upload" class="icon-only"></i>
        </button>
        <div class="progress">
            <div class="progress-bar" role="progressbar" aria-label="Vorschaubild" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </form>
    <?php if ($linked_exhibit->exhibit_preview() && $linked_exhibit->exhibit_preview()->isNotEmpty()) : ?>
        <form class="delete-form meta__button position-absolute" action="<?= $page->url() ?>" method="POST">
            <button type="submit" name="delete-preview" value="löschen" class="btn btn-primary">
                <i icon-name="trash-2" class="icon-only"></i>
            </button>
        </form>
    <?php endif ?>
</div>
<!-- asset upload -->
<div class="asset__area w-50 form__dnd position-relative">
    <form class="upload-form" action="<?= $page->url() ?>" enctype="multipart/form-data" method="POST">
        <label for="digital_asset">            
            <?= snippet('renderers/labeler', ['field' => 'digital_asset_label', 'fallback' => 'Video']) ?>
        </label>
        <?= snippet('renderers/input_element', ['_page' => $linked_exhibit, 'name' => 'digital_asset', 'extraClasses' => 'files', /* 'value' => $page->exhibit_preview(), */ 'type' => 'dnd', 'accept' => '.mp4']); ?>
        <button type="submit" name="save-asset" value="Mediendatei aktualisieren" class="btn btn-primary d-none">
            <i icon-name="upload" class="icon-only"></i>
        </button>
        <div class="progress">
            <div class="progress-bar" role="progressbar" aria-label="Mediendatei" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </form>
    <?php if ($linked_exhibit->digital_asset() && $linked_exhibit->digital_asset()->isNotEmpty()) : ?>
        <form class="delete-form meta__button position-absolute" action="<?= $page->url() ?>" method="POST">
            <button type="submit" name="delete-asset" value="löschen" class="btn btn-primary">
                <i icon-name="trash-2" class="icon-only"></i>
            </button>
        </form>
    <?php endif ?>
</div>