<?= js('/assets/js/vendor/model-viewer.min.js', ['type' => 'module']) ?>
<!-- image upload -->
<div class="preview__area form__dnd w-40 position-relative ms-auto">
    <form class="upload-form" action="<?= $page->url() ?>" enctype="multipart/form-data" method="POST">
        <label for="museum_preview">            
            <?= snippet('renderers/labeler', ['field' => 'museum_preview_label', 'fallback' => 'Vorschaubild für Museum']) ?>
        </label>
        <?php echo snippet('renderers/input_element', ['_page' => $linked_exhibit, 'name' => 'museum_preview', 'extraClasses' => 'files', /* 'value' => $page->exhibit_preview(), */ 'type' => 'dnd', 'accept' => '.jpg, .png, .heic, .jpeg']); ?>
        <button type="submit" name="save-museum-preview" value="Vorschaubild für Museum aktualisieren" class="btn btn-primary d-none">
            <i icon-name="upload" class="icon-only"></i>
        </button>
        <div class="progress">
            <div class="progress-bar" role="progressbar" aria-label="Vorschaubild" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </form>
    <?php if ($linked_exhibit->museum_preview() && $linked_exhibit->museum_preview()->isNotEmpty()) : ?>
        <form class="delete-form meta__button position-absolute" action="<?= $page->url() ?>" method="POST">
            <button type="submit" name="delete-museum-preview" value="löschen" class="btn btn-primary">
                <i icon-name="trash-2" class="icon-only"></i>
            </button>
        </form>
    <?php endif ?>
</div>
<!-- model upload -->
<!-- entfällt, da mitarbeiter das übernehmen -->
