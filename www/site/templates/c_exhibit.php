<?php snippet('header') ?>
<?php snippet('section-header', ['isFrontEnd' => true]) ?>

<?php
$curator = $page->linked_user()->toPageOrDraft();
$exhibition = $curator ? $curator->linked_exhibition()->toPageOrDraft() : false;
?>
<main>
    <div id="wrapper" class="exhibit">
        <div class="container custom">
            <?php if ($page->type() == '0') : ?>
                <?php snippet('renderers/viewer-3D', ['page' => $page]); ?>
            <?php elseif ($page->type() == '1') : ?>
                <?php snippet('renderers/viewer-embed', ['page' => $page]); ?>
            <?php elseif ($page->type() == '2') : ?>
                <?php snippet('renderers/viewer-digital', ['page' => $page]); ?>
            <?php endif ?>
            <section class="content ">
                <div class="row text-white">
                    <div class="col">
                        <h1 class="p-0 mb-1">
                            <?= $page->title()->html() ?>
                        </h1>
                    </div>
                </div>
                <div class="row justify-content-between text-white mt-5">
                    <div class="col col-12 col-lg-6 txt-info">
                        <?= $page->description()->kt() ?>
                        <?php if ($page->personal_relation()->isNotEmpty()) : ?>
                            <h3 class="mt-5">
                                Objektstory
                            </h3>
                            <?= $page->personal_relation()->kt() ?>
                        <?php endif ?>
                    </div>
                    <div class="col col-12 col-lg-5 meta-info">
                        <?= snippet('renderers/frontend/info_pair', ['context' => $page, 'label' => 'Thema', 'field' => 'impulse', 'type' => 'select']) ?>
                        <?= snippet('renderers/frontend/info_pair', ['context' => $page, 'label' => 'Objekttyp ', 'field' => 'type', 'type' => 'select']) ?>
                        <?= snippet('renderers/frontend/info_pair', ['context' => $page, 'label' => 'Klassifikation ', 'field' => 'classification', 'type' => 'multiselect']) ?>
                        <?php if ($page->type() == '0') : ?>
                            <!-- PHYSISCH -->
                            <?= snippet('renderers/frontend/info_pair', ['context' => $page, 'label' => 'Material', 'field' => 'material', 'type' => 'text']) ?>
                            <?= snippet('renderers/frontend/info_pair', ['context' => $page, 'label' => 'Maße (in cm)', 'field' => 'dimensions', 'type' => 'text']) ?>
                            <?= snippet('renderers/frontend/info_pair', ['context' => $page, 'label' => 'Gewicht (in g)', 'field' => 'weight', 'type' => 'text']) ?>
                            <?= snippet('renderers/frontend/info_pair', ['context' => $page, 'label' => 'Entstehungszeit', 'field' => 'made_when', 'type' => 'text']) ?>
                            <?= snippet('renderers/frontend/info_pair', ['context' => $page, 'label' => 'Aufnahmedatum Digitalisat', 'field' => 'scan_date', 'type' => 'date']) ?>
                        <?php elseif ($page->type() == '1') : ?>
                            <!-- EMBED -->
                            <?php if ($url = $page->embed_url()->yaml()) : ?>
                                <?= snippet('renderers/frontend/info_pair', ['context' => $page, 'label' => 'URL', 'forcedValue' => $url['input'] ?? '', 'type' => 'text']) ?>
                                <?= snippet('renderers/frontend/info_pair', ['context' => $page, 'label' => 'Provider', 'forcedValue' => $url['media']['providerName'] ?? '', 'type' => 'text']) ?>
                            <?php endif ?>
                        <?php elseif ($page->type() == '2') : ?>
                            <!-- DIGITAL -->
                        <?php endif ?>
                        <?= snippet('renderers/frontend/info_pair', ['context' => $curator, 'label' => 'Benutzer', 'field' => 'username', 'type' => 'text']) ?>
                        <?= snippet('renderers/frontend/info_pair', ['context' => $curator, 'label' => 'Klassenstufe', 'field' => 'schoolclass', 'type' => 'select']) ?>
                        <?= snippet('renderers/frontend/info_pair', ['context' => $curator, 'label' => 'Bundesland', 'field' => 'curator_state', 'type' => 'select']) ?>
                        <?php if ($exhibition) : ?>
                            <div class="row mt-5">
                                <p class="label col col-12 mb-0">
                                    Teil der Ausstellung:
                                </p>
                                <p class="exhibition-title col col-12">
                                    <a title="<?= $exhibition->title()->html() ?>" href="<?= $exhibition->url() ?>">
                                        <?= $exhibition->title()->html() ?> ›
                                    </a>
                                </p>
                            </div>

                        <?php endif ?>
                    </div>
                </div>
            </section>
        </div>

    </div>
</main>
<?php snippet('footer', ['isFrontEnd' => true]) ?>