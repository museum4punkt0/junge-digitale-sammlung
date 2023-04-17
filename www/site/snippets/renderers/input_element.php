<?php

if (isset($_page)) {
    if (isset($forcePageValues)) {
        $value = $_page->content()->get($name)->value();
    } else {
        $value = $data[$name] ?? $_page->content()->get($name)->value();
    }
} elseif (isset($forcedValue)) {
    $value = $forcedValue;
} else {
    $value = ''; // due to no page in exhibitions
}

if (!isset($extraAttributes)) {
    $extraAttributes = '';
}

if (!isset($extraClasses)) {
    $extraClasses = '';
}

if (isset($bpField)) {
    $isRequiredAtEnd = $bpField['requiredForPublishing'];
    if ($isRequiredAtEnd) {
        $extraClasses .= ' is-required';
    }
}

if (isset($disabled) && $disabled) {
    $extraClasses .= ' force-disabled';
}

if ($value == null) {
    $value = ''; // in case passed value was null
}

$ajaxOnChange = "";
if (isset($ajaxHandler)) {
    $ajaxOnChange = "data-ajax-handler='" . $ajaxHandler . "'";
}

$onChange = "";
if (isset($changeHandler)) {
    $onChange = "data-change-handler='" . $changeHandler . "'";
}

?>


<?php if ($type === "select" || $type === 'multiselect') : ?>
    <!-- TYPE SELECT -->
    <?php
    // strip empty spaces for check if selected or not
    $value = preg_replace('/\s+/', '', $value);
    $multiWithGroups = false;
    $selectClasses = "";
    if (isset($optgroup) && $optgroup) {
        $multiWithGroups = true;
        $selectClasses .= "optgroup";
    }

    if (isset($group))
        $selectClasses .= " select-has-group";
    ?>
    <?php if (isset($selectPopulatorData)) : ?>
        <div <?= $extraAttributes ?> <?php if (isset($disabled) && $disabled) : ?>disabled<?php endif ?> <?= $ajaxOnChange ?> <?= $onChange ?> <?= $required ?? '' ?> name="<?= $name ?>" value="<?= $value ?>" id="<?= $name ?>" class="<?= $extraClasses ?> <?= $selectClasses ?>" <?php if (isset($group)) : ?> data-select-group="<?= $group ?>" <?php endif ?> <?php if ($type === 'multiselect') : ?> multiple <?php endif ?> <?php if (isset($selectPopulatorData)) : ?> json-path="<?= $selectPopulatorData ?>" <?php endif ?>>
        </div>
    <?php else : ?>
        <?php
        if (isset($selectPopulator))
            $options = $selectPopulator;
        else
            $options = site()->data_populators_pick()->toPage()->content()->get($name)->toStructure();
        ?>

        <select <?= $extraAttributes ?> <?= $ajaxOnChange ?> <?= $onChange ?> <?php if (isset($disabled) && $disabled) : ?>disabled<?php endif ?> <?= $required ?? '' ?> name="<?= $name ?>" id="<?= $name ?>" class="<?= $extraClasses ?> <?= $selectClasses ?>" <?php if (isset($group)) : ?> data-select-group="<?= $group ?>" <?php endif ?> <?php if ($type === 'multiselect') : ?> multiple <?php endif ?>>

            <?php
            if ($value == null) $value = '';
            $value = explode(',', $value);
            foreach ($options as $option) : ?>
                <?php if ($option) : ?>
                    <?php if ($option->sub_options() && $option->sub_options()->isNotEmpty()) : ?>
                        <?php $suboptions = $option->sub_options()->toStructure(); ?>
                        <optgroup label="<?= $option->desc() ?>">
                            <?php foreach ($suboptions as $suboption) : ?>
                                <option <?= e(in_array($option->id() . '-' . $suboption->id(), $value), 'selected', '') ?> value="<?= $option->id() . '-' . $suboption->id() ?>"> <?= $suboption->desc() ?></option>
                            <?php endforeach ?>
                        </optgroup>
                    <?php else : ?>
                        <option <?php if ($option->hasExhibitionAlready()->value() == true) : ?>disabled <?php endif ?> <?= e(in_array($option->id(), $value), 'selected', '') ?> value="<?= $option->id() ?>"> <?= $option->desc() ?></option>
                    <?php endif ?>
                <?php endif ?>
            <?php endforeach ?>
        </select>
    <?php endif ?>
<?php elseif ($type === "range") : ?>
    <!-- TYPE RANGE -->
    <input <?= $extraAttributes ?> <?php if (isset($disabled) && $disabled) : ?>disabled<?php endif ?> <?= $ajaxOnChange ?> <?= $onChange ?> class="form-control <?= $extraClasses ?>" type="range" id="<?= $name ?>" name="<?= $name ?>" value="<?= $value ?>" step="<?= $step ?>" min="<?= $min ?>" max="<?= $max ?>">
<?php elseif ($type === "textarea") : ?>
    <!-- TYPE TEXTAREA -->
    <textarea rows="6" <?= $extraAttributes ?> <?php if (isset($disabled) && $disabled) : ?>disabled<?php endif ?> <?= $ajaxOnChange ?> <?= $onChange ?> <?= $required ?? '' ?> name="<?= $name ?>" id="<?= $name ?>" class="form-control <?= $extraClasses ?>"><?= $value ?></textarea>
<?php elseif ($type === "date") : ?>
    <!-- TYPE DATE -->
    <input <?= $extraAttributes ?> <?php if (isset($disabled) && $disabled) : ?>disabled<?php endif ?> <?= $ajaxOnChange ?> <?= $onChange ?> <?= $required ?? '' ?> type="date" id="<?= $name ?>" class="form-control <?= $extraClasses ?>" name="<?= $name ?>" value="<?= $value ?>" max="<?= date('Y-m-d'); ?>">
<?php elseif ($type === "dnd") : ?>
    <!-- TYPE DRAG N DROP -->
    <div class="drag-area">
        <div class="drop-area">
            <div class="media-container">
                <?php if ($_file = $_page->content()->get($name)->toFile()) : ?>
                    <?php if ($name == 'exhibit_preview' || $name == 'museum_preview') : ?>
                        <?= $_file->responsiveImg() ?>
                    <?php elseif ($name == 'threed_model') : ?>
                        <model-viewer class="exhibit-3d" id="reveal" interaction-prompt="none" loading="eager" exposure="<?= $_page->threed_model_light() ?>" camera-controls touch-action="pan-y" auto-rotate src="<?= $_file->url() ?>" shadow-intensity="1" style="background-color: unset;"></model-viewer>
                    <?php elseif ($name == 'digital_asset') : ?>
                        <video id="<?= $_file->id() ?>" class="vlite-js modal__video" src="<?= $_file->url() ?>" crossorigin>
                        </video>
                    <?php endif ?>
                <?php endif ?>
            </div>
            <header class="text-center">Datei hier rein ziehen</header>
            <span>ODER</span>
        </div>
        <label class="btn btn-primary input-file__label w-100">
            <input <?= $extraAttributes ?> <?php if (isset($disabled) && $disabled) : ?>disabled<?php endif ?> <?= $ajaxOnChange ?> <?= $onChange ?> accept="<?= $accept ?>" id="<?= $name ?>" name="<?= $name ?>" type="file" class="form-control <?= $extraClasses ?>">
            <span>Datei auswählen</span>
        </label>
    </div>
<?php elseif ($type === "radioimages") : ?>
    <?php
    $value = preg_replace('/\s+/', '', $value);
    $value = str_replace("-", "", $value);
    ?>
    <div <?= $ajaxOnChange ?> <?= $onChange ?> id="<?= $name ?>" class="radioimages row mx-0 g-3 mb-4" name="<?= $name ?>" value='<?= $value ?>'>
        <div class="col-4 col-lg-3">
            <div class="custom-control custom-radio image-checkbox">
                <?php if ($value == "") : ?>
                    <input type="radio" class="custom-control-input radioimage-radio <?= $extraClasses ?>" id="no-preview-selected" name="<?= $name ?>" value="" checked>
                <?php else : ?>
                    <input type="radio" class="custom-control-input radioimage-radio <?= $extraClasses ?>" id="no-preview-selected" name="<?= $name ?>" value="">
                <?php endif ?>
                <label class="custom-control-label bg-light text-center" for="no-preview-selected">
                    <span class="text-muted opacity-75">
                        <i icon-name="slash"></i>
                        <div class="mt-2 d-none d-lg-block">Kein Bild</div>
                    </span>
                </label>
            </div>
        </div>
        <?php $collection = $_page->getPreviewImages();
        foreach ($collection as $key => $item) :
        ?>
            <div class="col-4 col-lg-3">
                <div class="custom-control custom-radio image-checkbox">
                    <?php if ($value == $item->uuid()->toString()) : ?>
                        <input type="radio" class="custom-control-input radioimage-radio <?= $extraClasses ?>" id="<?= $item->name() ?>" name="<?= $name ?>" value="<?= $item->uuid()->toString() ?>" checked>
                    <?php else : ?>
                        <input type="radio" class="custom-control-input radioimage-radio <?= $extraClasses ?>" id="<?= $item->name() ?>" name="<?= $name ?>" value="<?= $item->uuid()->toString() ?>">
                    <?php endif ?>
                    <label class="custom-control-label" for="<?= $item->name() ?>">
                        <?= $item->responsiveImg('galleryThumb') ?>
                    </label>
                </div>
            </div>
        <?php endforeach ?>
    </div>
<?php else : ?>
    <!-- TYPE TEXT: DEFAULT -->
    <input <?= $extraAttributes ?> <?php if (isset($disabled) && $disabled) : ?>disabled<?php endif ?> <?= $ajaxOnChange ?> <?= $onChange ?> <?= $required ?? '' ?> id="<?= $name ?>" class="form-control <?= $extraClasses ?>" name="<?= $name ?>" type="<?= $type ?>" value="<?= $value ?>">
<?php endif ?>