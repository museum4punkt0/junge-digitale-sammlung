
<?php

$_layouts = $layouts->isNotEmpty() ? $layouts->toLayouts() : [];

foreach ($_layouts as $layout) : ?>
    <?php
    $customstyle = "";
    $useCustom = false;
    if ($layout->sectioncolor()->isNotEmpty()) {
        $useCustom      = true;
        $palette        = $layout->sectioncolor()->yaml();
        $background     = $palette["background"];
        $text           = $palette["text"];
        $customstyle    = "style='--custom-color-bg: " . $background . "; --custom-color-txt: " . $text . "'";
    }
    ?>
    <section <?= $customstyle ?> class="section <?= e($useCustom, 'custom-color' ) ?> regular <?= $layout->sectiontype() ?>">
        <div class="container">
            <div class="row <?= $layout->valign() ?>">
                <?php foreach ($layout->columns() as $column) : ?>
                    <div class="col col-12 col-lg-<?= $column->span() ?>">
                        <?= $column->blocks() ?>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </section>
<?php endforeach ?>