<?php snippet('header') ?>
<?php snippet('section-header', ['isFrontEnd' => true]) ?>

<?php
$curators = [$page->user1()->toPageOrDraft(), $page->user2()->toPageOrDraft(), $page->user3()->toPageOrDraft(), $page->user4()->toPageOrDraft(), $page->user5()->toPageOrDraft()];

$exhibits = [];
foreach ($curators as $curator) {
    if ($curator) {
        array_push($exhibits, $curator->linked_exhibit()->toPageOrDraft());
    } else {
        array_push($exhibits, false);
    }
}

$exhibit_texts = [$page->exhibit1text()->kt(), $page->exhibit2text()->kt(), $page->exhibit3text()->kt(), $page->exhibit4text()->kt(), $page->exhibit5text()->kt()];

$actual_exhibits = $page->getLinkedTotalExhibitsCount();
?>
<main>
    <div id="wrapper" class="exhibition">
        <div class="container custom">
            <div class="podest-container justify-content-between align-items-end" style="--actual_exhibits_count: <?= $actual_exhibits ?> ;">
                <?php for ($x = 0; $x < count($exhibits); $x++) : ?>
                    <?php if ($curators[$x] && $exhibits[$x]) : ?>
                        <div class="col-exhibit">
                            <?= snippet('renderers/frontend/exhibit-preview-renderer', ['user' => $curators[$x]]); ?>
                            <div class="exhibit-podest"> </div>
                        </div>
                    <?php endif ?>
                <?php endfor ?>
            </div>
        </div>

        <div class="container custom">
            <section class="content ">
                <div class="row text-white">
                    <div class="col">
                        <p class="meta">
                            <?= $page->impulse()->mapValueToLabel() ?>
                        </p>
                    </div>
                </div>
                <div class="row text-white">
                    <div class="col">
                        <h1>
                            <?= $page->title()->html() ?>
                        </h1>
                    </div>
                </div>
                <div class="row row-copy-text justify-content-between text-white">
                    <div class="col col-12 col-lg-5 exhibition-intro">
                        <?= $page->exhibitionintro()->kt() ?>
                    </div>
                    <div class="col gx-0 col-12 col-lg-7">
                        <div class="row" data-masonry='{"percentPosition": true }'>
                            <?php for ($x = 0; $x < count($curators); $x++) : ?>
                                <?php if ($curators[$x] && $exhibits[$x]) : ?>
                                    <?= snippet('renderers/frontend/exhibit-text-renderer', ['user' => $curators[$x], 'exhibittext' => $exhibit_texts[$x]]); ?>
                                <?php endif ?>
                            <?php endfor ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>

    </div>
</main>
<?php snippet('footer', ['isFrontEnd' => true]) ?>