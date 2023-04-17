<div class="form-group row">
    <label for="fullname" class="col-md-3 col-form-label is-required">
        <?= snippet('renderers/labeler', ['field' => 'fullname_label', 'fallback' => 'Vorname und erster Buchstabe vom Nachnamen']) ?>
    </label>
    <div class="col-md-7">
        <?= snippet('renderers/input_element', ['forcedValue' => $data['fullname'] ?? $page->title(), 'name' => 'fullname', 'type' => 'text', 'required' => 'required']); ?>
    </div>
    <div class="col-md-2">
        <?= snippet('renderers/fields_metainfo', ['name' => 'fullname']) ?>
    </div>
</div>
<div class="form-group row">
    <label for="username" class="col-md-3 col-form-label is-required">
        <?= snippet('renderers/labeler', ['field' => 'username_label', 'fallback' => 'Benutzername']) ?>
    </label>
    <div class="col-md-7">
        <?= snippet('renderers/input_element', ['_page' => $page, 'name' => 'username', 'type' => 'text', 'extraAttributes' => 'original-data="' . $page->username() . '"', 'ajaxHandler' => 'checkUsername']); ?>
    </div>
    <div class="col-md-2">
        <?= snippet('renderers/fields_metainfo', ['name' => 'username']) ?>
    </div>
</div>
<div class="form-group row">
    <label for="gender" class="col-md-3 col-form-label">
        <?= snippet('renderers/labeler', ['field' => 'gender_label', 'fallback' => 'Geschlecht']) ?>
    </label>
    <div class="col-md-7">
        <?= snippet('renderers/input_element', ['_page' => $page, 'name' => 'gender', 'type' => 'select']); ?>
    </div>
    <div class="col-md-2">
        <?= snippet('renderers/fields_metainfo', ['name' => 'gender']) ?>
    </div>
</div>
<div class="form-group row">
    <label for="age_in_years" class="col-md-3 col-form-label">        
        <?= snippet('renderers/labeler', ['field' => 'age_in_years_label', 'fallback' => 'Alter']) ?>
    </label>
    <div class="col-md-7">
        <?= snippet('renderers/input_element', ['_page' => $page, 'name' => 'age_in_years', 'type' => 'select', 'selectPopulator' => populateAge()]); ?>
    </div>
    <div class="col-md-2">
        <?= snippet('renderers/fields_metainfo', ['name' => 'age_in_years']) ?>
    </div>
</div>
<div class="form-group row">
    <label for="birthcountry" class="col-md-3 col-form-label">        
        <?= snippet('renderers/labeler', ['field' => 'birthcountry_label', 'fallback' => 'Geburtsland']) ?>
    </label>
    <div class="col-md-7">
        <?= snippet('renderers/input_element', ['_page' => $page, 'name' => 'birthcountry', 'type' => 'select', 'selectPopulatorData' => $kirby->url() . "/countries"]); ?>
    </div>
    <div class="col-md-2">
        <?= snippet('renderers/fields_metainfo', ['name' => 'birthcountry']) ?>
    </div>
</div>
<div class="form-group row">
    <label for="birthcountry_comment" class="col-md-3 col-form-label">        
        <?= snippet('renderers/labeler', ['field' => 'birthcountry_comment_label', 'fallback' => 'Geburtsland [Hist. Länder]']) ?>
    </label>
    <div class="col-md-7">
        <?= snippet('renderers/input_element', ['_page' => $page, 'name' => 'birthcountry_comment', 'type' => 'text']); ?>
    </div>
    <div class="col-md-2">
        <?= snippet('renderers/fields_metainfo', ['name' => 'birthcountry_comment']) ?>
    </div>
</div>
<div class="form-group row">
    <label for="stations" class="col-md-3 col-form-label">        
        <?= snippet('renderers/labeler', ['field' => 'stations_label', 'fallback' => 'Lebensstationen']) ?>
    </label>
    <div class="col-md-7">
        <?= snippet('renderers/input_element', ['_page' => $page, 'name' => 'stations',  'type' => 'multiselect', 'selectPopulatorData' => $kirby->url() . "/countries"]); ?>
    </div>
    <div class="col-md-2">
        <?= snippet('renderers/fields_metainfo', ['name' => 'stations']) ?>
    </div>
</div>
<div class="form-group row">
    <label for="stations_comment" class="col-md-3 col-form-label">        
        <?= snippet('renderers/labeler', ['field' => 'stations_comment_label', 'fallback' => 'Lebensstationen [Hist. Länder]']) ?>
    </label>
    <div class="col-md-7">
        <?= snippet('renderers/input_element', ['_page' => $page, 'name' => 'stations_comment', 'type' => 'text']); ?>
    </div>
    <div class="col-md-2">
        <?= snippet('renderers/fields_metainfo', ['name' => 'stations_comment']) ?>
    </div>
</div>
<div class="form-group row">
    <label for="personaldrive" class="col-md-3 col-form-label">        
        <?= snippet('renderers/labeler', ['field' => 'personaldrive_label', 'fallback' => 'Folgende Zugehörigkeiten sind mir wichtig:']) ?>
    </label>
    <div class="col-md-7">
        <?= snippet('renderers/input_element', ['_page' => $page, 'name' => 'personaldrive', 'type' => 'multiselect']); ?>
    </div>
    <div class="col-md-2">
        <?= snippet('renderers/fields_metainfo', ['name' => 'personaldrive']) ?>
    </div>
</div>
<div class="form-group row">
    <label for="curator_state" class="col-md-3 col-form-label">        
        <?= snippet('renderers/labeler', ['field' => 'curator_state_label', 'fallback' => 'Bundesland']) ?>
    </label>
    <div class="col-md-7">
        <?= snippet('renderers/input_element', ['_page' => $page, 'name' => 'curator_state', 'type' => 'select']); ?>
    </div>
    <div class="col-md-2">
        <?= snippet('renderers/fields_metainfo', ['name' => 'curator_state']) ?>
    </div>
</div>
<div class="form-group row">
    <label for="schoolclass" class="col-md-3 col-form-label">        
        <?= snippet('renderers/labeler', ['field' => 'schoolclass_label', 'fallback' => 'Klassenstufe']) ?>
    </label>
    <div class="col-md-7">
        <?= snippet('renderers/input_element', ['_page' => $page, 'name' => 'schoolclass', 'type' => 'select']); ?>
    </div>
    <div class="col-md-2">
        <?= snippet('renderers/fields_metainfo', ['name' => 'schoolclass']) ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-7 offset-md-3">
        <div class="form-check">
            <input type="hidden" id="dseHidden" name="dse" class="onCheckHidden" value="">
            <?php
            $dseChecked = $data['dse'] ?? $page->dse();
            ?>
            <input class="form-check-input" type="checkbox" id="dse" name="dse" <?php if ($dseChecked != '') : ?> checked <?php endif ?>>
            <?php
            if ($dataPage)
                $infoText = $dataPage->privacytext();
            else
                $infoText = 'Datenschutz Text';
            ?>
            <label class="form-check-label" for="dse" class="col" for="dse"><?= $infoText ?></label>
        </div>
    </div>

</div>