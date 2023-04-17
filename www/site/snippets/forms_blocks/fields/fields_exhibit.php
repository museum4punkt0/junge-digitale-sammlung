<?php if ($linkedpage->type() == '1') : ?>
    <!-- DIGITAL -->
    <div class="form-group row">
        <label for="embed_url" class="col-md-3 col-form-label is-required">
            <?= snippet('renderers/labeler', ['field' => 'embed_url_label', 'fallback' => 'URL / Link']) ?>
        </label>
        <div class="col-md-7">
            <?php $urldata = $linkedpage->embed_url()->yaml(); ?>
            <?= snippet('renderers/input_element', ['forcedValue' => $urldata['input'] ?? null, 'name' => 'embed_url', 'type' => 'url', 'ajaxHandler' => 'scrapWorkshopEmbed']); ?>
        </div>
        <div class="col-md-2">
            <?= snippet('renderers/fields_metainfo', ['name' => 'embed_url']) ?>
        </div>
    </div>
<?php endif ?>

<!-- regular -->
<div class="form-group row">
    <label for="type" class="col-md-3 col-form-label is-required">        
        <?= snippet('renderers/labeler', ['field' => 'type_label', 'fallback' => 'Objekttyp']) ?>
    </label>
    <div class="col-md-7">
        <?= snippet('renderers/input_element', ['_page' => $linkedpage, 'name' => 'type', 'type' => 'select', 'changeHandler' => 'handleExhibitTypeChange', 'disabled' => false]); ?>
    </div>
    <div class="col-md-2">
        <?= snippet('renderers/fields_metainfo', ['name' => 'type']) ?>
    </div>
</div>
<div class="form-group row">
    <label for="exhibitname" class="col-md-3 col-form-label is-required">        
        <?= snippet('renderers/labeler', ['field' => 'exhibitname_label', 'fallback' => 'Objekttitel']) ?>
    </label>
    <div class="col-md-7">
        <?= snippet('renderers/input_element', ['forcedValue' => $data['exhibitname'] ?? $linkedpage->title(), 'name' => 'exhibitname', 'type' => 'text', 'required' => 'required']); ?>
    </div>
    <div class="col-md-2">
        <?= snippet('renderers/fields_metainfo', ['name' => 'exhibitname']) ?>
    </div>
</div>
<div class="form-group row">
    <label for="impulse" class="col-md-3 col-form-label is-required">        
        <?= snippet('renderers/labeler', ['field' => 'impulse_label', 'fallback' => 'Thema']) ?>
    </label>
    <div class="col-md-7">
        <?= snippet('renderers/input_element', ['_page' => $linkedpage, 'name' => 'impulse', 'type' => 'select', 'ajaxHandler' => 'checkImpulseRelationships']); ?>
    </div>
    <div class="col-md-2">
        <?= snippet('renderers/fields_metainfo', ['name' => 'impulse']) ?>
    </div>
</div>
<div class="form-group row">
    <label for="classification" class="col-md-3 col-form-label is-required">        
        <?= snippet('renderers/labeler', ['field' => 'classification_label', 'fallback' => 'Klassifikation']) ?>
    </label>
    <div class="col-md-7">
        <?= snippet('renderers/input_element', ['_page' => $linkedpage, 'name' => 'classification', 'type' => 'multiselect', 'optgroup' => true]); ?>
    </div>
    <div class="col-md-2">
        <?= snippet('renderers/fields_metainfo', ['name' => 'classification']) ?>
    </div>
</div>
<div class="form-group row">
    <label for="description" class="col-md-3 col-form-label is-required">        
        <?= snippet('renderers/labeler', ['field' => 'description_label', 'fallback' => 'Objektbeschreibung']) ?>
    </label>
    <div class="col-md-7">
        <?= snippet('renderers/input_element', ['_page' => $linkedpage, 'name' => 'description', 'type' => 'textarea']); ?>
    </div>
    <div class="col-md-2">
        <?= snippet('renderers/fields_metainfo', ['name' => 'description']) ?>
    </div>
</div>
<div class="form-group row">
    <label for="made_when" class="col-md-3 col-form-label">        
        <?= snippet('renderers/labeler', ['field' => 'made_when_label', 'fallback' => 'Entstehungszeit']) ?>
    </label>
    <div class="col-md-7">
        <?= snippet('renderers/input_element', ['_page' => $linkedpage, 'name' => 'made_when', 'type' => 'text']); ?>
    </div>
    <div class="col-md-2">
        <?= snippet('renderers/fields_metainfo', ['name' => 'made_when']) ?>
    </div>
</div>
<div class="form-group row">
    <label for="historical_background" class="col-md-3 col-form-label">        
        <?= snippet('renderers/labeler', ['field' => 'historical_background_label', 'fallback' => 'Historischer, kultureller Hintergrund']) ?>
    </label>
    <div class="col-md-7">
        <?= snippet('renderers/input_element', ['_page' => $linkedpage, 'name' => 'historical_background', 'type' => 'textarea']); ?>
    </div>
    <div class="col-md-2">
        <?= snippet('renderers/fields_metainfo', ['name' => 'historical_background']) ?>
    </div>
</div>
<div class="form-group row">
    <label for="personal_relation" class="col-md-3 col-form-label is-required">        
        <?= snippet('renderers/labeler', ['field' => 'personal_relation_label', 'fallback' => 'Objektstory']) ?>
    </label>
    <div class="col-md-7">
        <?= snippet('renderers/input_element', ['_page' => $linkedpage, 'name' => 'personal_relation', 'type' => 'textarea']); ?>
    </div>
    <div class="col-md-2">
        <?= snippet('renderers/fields_metainfo', ['name' => 'personal_relation']) ?>
    </div>
</div>

<?php if ($linkedpage->type() == '0') : ?>
    <!-- PHYSISCH -->
    <div class="form-group row">
        <label for="material" class="col-md-3 col-form-label">            
            <?= snippet('renderers/labeler', ['field' => 'material_label', 'fallback' => 'Material']) ?>
        </label>
        <div class="col-md-7">
            <?= snippet('renderers/input_element', ['_page' => $linkedpage, 'name' => 'material', 'type' => 'text']); ?>
        </div>
        <div class="col-md-2">
            <?= snippet('renderers/fields_metainfo', ['name' => 'material']) ?>
        </div>
    </div>
    <div class="form-group row">
        <label for="dimensions" class="col-md-3 col-form-label">            
            <?= snippet('renderers/labeler', ['field' => 'dimensions_label', 'fallback' => 'Maße (in cm)']) ?>
        </label>
        <div class="col-md-7">
            <?= snippet('renderers/input_element', ['_page' => $linkedpage, 'name' => 'dimensions', 'type' => 'text']); ?>
        </div>
        <div class="col-md-2">
            <?= snippet('renderers/fields_metainfo', ['name' => 'dimensions']) ?>
        </div>
    </div>
    <div class="form-group row">
        <label for="weight" class="col-md-3 col-form-label">            
            <?= snippet('renderers/labeler', ['field' => 'weight_label', 'fallback' => 'Gewicht (in g)']) ?>
        </label>
        <div class="col-md-7">
            <?= snippet('renderers/input_element', ['_page' => $linkedpage, 'name' => 'weight', 'type' => 'text']); ?>
        </div>
        <div class="col-md-2">
            <?= snippet('renderers/fields_metainfo', ['name' => 'weight']) ?>
        </div>
    </div>
    <div class="form-group row">
        <label for="scan_date" class="col-md-3 col-form-label">            
            <?= snippet('renderers/labeler', ['field' => 'scan_date_label', 'fallback' => 'Aufnahmedatum Digitalisat']) ?>
        </label>
        <div class="col-md-7">
            <?= snippet('renderers/input_element', ['_page' => $linkedpage, 'name' => 'scan_date', 'type' => 'date']); ?>
        </div>
        <div class="col-md-2">
            <?= snippet('renderers/fields_metainfo', ['name' => 'scan_date']) ?>
        </div>
    </div>
    <div class="form-group row">
        <label for="scan_method" class="col-md-3 col-form-label">            
            <?= snippet('renderers/labeler', ['field' => 'scan_method_label', 'fallback' => 'Digitalisierungsmethode']) ?>
        </label>
        <div class="col-md-7">
            <?= snippet('renderers/input_element', ['_page' => $linkedpage, 'name' => 'scan_method', 'type' => 'select']); ?>
        </div>
        <div class="col-md-2">
            <?= snippet('renderers/fields_metainfo', ['name' => 'scan_method']) ?>
        </div>
    </div>
    <div class="form-group row">
        <label for="scan_device" class="col-md-3 col-form-label is-required">            
            <?= snippet('renderers/labeler', ['field' => 'scan_device_label', 'fallback' => 'Digitalisierungsgerät']) ?>
        </label>
        <div class="col-md-7">
            <?= snippet('renderers/input_element', ['_page' => $linkedpage, 'name' => 'scan_device', 'type' => 'select']); ?>
        </div>
        <div class="col-md-2">
            <?= snippet('renderers/fields_metainfo', ['name' => 'scan_device']) ?>
        </div>
    </div>
    <div class="form-group row">
        <label for="scan_software" class="col-md-3 col-form-label">            
            <?= snippet('renderers/labeler', ['field' => 'scan_software_label', 'fallback' => 'Software, Version']) ?>
        </label>
        <div class="col-md-7">
            <?= snippet('renderers/input_element', ['_page' => $linkedpage, 'name' => 'scan_software', 'type' => 'text']); ?>
        </div>
        <div class="col-md-2">
            <?= snippet('renderers/fields_metainfo', ['name' => 'scan_software']) ?>
        </div>
    </div>
    <div class="form-group row">
        <label for="scan_updated" class="col-md-3 col-form-label">            
            <?= snippet('renderers/labeler', ['field' => 'scan_updated_label', 'fallback' => 'Bearbeitung des Digitalisates']) ?>
        </label>
        <div class="col-md-7">
            <?= snippet('renderers/input_element', ['_page' => $linkedpage, 'name' => 'scan_updated', 'type' => 'text']); ?>
        </div>
        <div class="col-md-2">
            <?= snippet('renderers/fields_metainfo', ['name' => 'scan_updated']) ?>
        </div>
    </div>
<?php endif ?>