<div class="dynamic__content">
    <?php
    if (isset($curator) && $curator->isNotEmpty())
        $impulseDisabled = true;
    else
        $impulseDisabled = false;
    ?>
    <div class="form-group row">
        <label for="exhibitiontitle" class="col-md-3 col-form-label is-required">            
            <?= snippet('renderers/labeler', ['field' => 'exhibitiontitle_label', 'fallback' => 'Ausstellungstitel']) ?>
        </label>
        <div class="col-md-7">
            <?= snippet('renderers/input_element', ['forcedValue' => isset($exhibition) ? $exhibition->title() : 'Neue Ausstellung-' . time(), 'name' => 'exhibitiontitle', 'type' => 'text', 'required' => 'required']); ?>
        </div>
        <div class="col-md-2">
            <?= snippet('renderers/fields_metainfo', ['name' => 'exhibitiontitle']) ?>
        </div>
    </div>
    <div class="form-group row">
        <label for="impulse" class="col-md-3 col-form-label is-required">            
            <?= snippet('renderers/labeler', ['field' => 'impulse_label', 'fallback' => 'Thema']) ?>
        </label>
        <div class="col-md-7">
            <?= snippet('renderers/input_element', ['_page' => $exhibition ?? null, 'forcePageValues' => true, 'name' => 'impulse', 'type' => 'select', 'required' => 'required', 'disabled' => $impulseDisabled /* , 'ajaxHandler' => 'populateTopicalExhibits' */]); ?>
        </div>
        <div class="col-md-2">
            <?= snippet('renderers/fields_metainfo', ['name' => 'impulse']) ?>
        </div>
    </div>
    <div class="form-group row">
        <label for="exhibitionintro" class="col-md-3 col-form-label  is-required">            
            <?= snippet('renderers/labeler', ['field' => 'exhibitionintro_label', 'fallback' => 'Einführungstext']) ?>
        </label>
        <div class="col-md-7">
            <?= snippet('renderers/input_element', ['_page' => $exhibition ?? null, 'forcePageValues' => true, 'name' => 'exhibitionintro', 'type' => 'textarea']); ?>
        </div>
        <div class="col-md-2">
            <?= snippet('renderers/fields_metainfo', ['name' => 'exhibitionintro']) ?>
        </div>
    </div>
    <?php
    if (isset($exhibition)) {
        snippet('forms_blocks/fields/fields_exhibit_generator', ['_page' => $exhibition ?? null, 'curator' => $curator ?? null]);
    } else {
        snippet('forms_blocks/fields/fields_exhibit_generator', ['page' => $page ?? null]);
    }
    ?>
</div>