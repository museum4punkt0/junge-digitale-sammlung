<?php
$valueField;
if (isset($forcedValue))
    $valueField = $forcedValue;
else if ($context->content()->get($field) && $context->content()->get($field)->isNotEmpty() && $context->content()->get($field) != '')
    $valueField = $context->content()->get($field);

?>
<?php if (isset($valueField) && $valueField != '') : ?>
    <div class="row">
        <div class="col col-12 col-sm-3 col-lg-5">
            <p class="label">
                <?= $label ?>
            </p>
        </div>
        <div class="col col-12 col-sm-9 col-lg-7">
            <?php if ($type == 'select') : ?>
                <?php
                $options = $site->data_populators_pick()->toPage()->content()->get($field)->toStructure();
                foreach ($options as $option) : ?>
                    <?php if ($option->id() == $valueField) : ?>
                        <p>
                            <?= $option->desc() ?>
                        </p>
                    <?php endif ?>
                <?php endforeach ?>
            <?php elseif ($type == 'multiselect') : ?>
                <?php
                $multiValue = "";
                $valueField = str_replace(" ", "", $valueField->value());
                $valueField = explode(',', $valueField);
                ?>
                <?php
                $options = $site->data_populators_pick()->toPage()->content()->get($field)->toStructure();
                foreach ($options as $option) : ?>
                    <?php if (in_array($option->id(), $valueField)) : ?>
                        <?php $multiValue .= $option->desc() . ", " ?>
                    <?php endif ?>
                    <?php if ($option->sub_options() && $option->sub_options()->isNotEmpty()) : ?>
                        <?php $suboptions = $option->sub_options()->toStructure(); ?>
                        <?php foreach ($suboptions as $suboption) : ?>
                            <?php if (in_array($option->id() . '-' . $suboption->id(), $valueField)) : ?>
                                <?php $multiValue .= $suboption->desc() . ", " ?>
                            <?php endif ?>
                        <?php endforeach ?>
                    <?php endif ?>

                <?php endforeach ?>
                <p>
                    <?= rtrim($multiValue, ', '); ?>
                </p>
            <?php elseif ($type == 'date') : ?>
                <p><?= $valueField->toDate('d.m.Y') ?></p>
            <?php else : ?>
                <p><?= $valueField ?></p>
            <?php endif ?>
        </div>
    </div>
<?php endif ?>