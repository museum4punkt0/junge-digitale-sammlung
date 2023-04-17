<?php snippet('header') ?>
<?php snippet('section-header', ['isFrontEnd' => true]) ?>

<main>
    <div id="wrapper">
        <div class="container">
            <h1>
                <?= $page->title()->html() ?>
            </h1>
        </div>
        <?php snippet('renderers/layouts-renderer', ['layouts' => $page->layout()]) ?>
    </div>
</main>
<?php snippet('footer', ['isFrontEnd' => true]) ?>