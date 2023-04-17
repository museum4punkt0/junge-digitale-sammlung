<?php snippet('header') ?>
<?php snippet('section-header') ?>
<main>
    <div id="wrapper">
        <div class="container">
            <h1>
                <?= $data['fullname'] ?? $page->title() ?>
            </h1>
        </div>
        <?php if ($authenticated) : ?>
            <!-- CURATOR -->
            <?php if ($page->checkPin()) : ?>
                <?php snippet('renderers/alert'); ?>
                <?php snippet('forms_blocks/curator_leader_form', ['data' => $data]); ?>
                <?php snippet('renderers/modal_confirmation'); ?>
            <?php else : ?>
                <div>
                    <p>
                        Diese Seite gehört einem anderen Teilnehmer.
                    </p>
                    <a href="<?= $page->parent()->url() ?>">
                        Zum Workshop ›
                    </a>
                </div>
            <?php endif ?>
        <?php else : ?>
            <p>Bitte loggen Sie sich ein.</p>
            <a href="<?= kirby()->url() . '/login' ?>">Zum Login »</a>
        <?php endif ?>
    </div>
</main>

<?= js('/assets/js/vendor/virtual-select.js') ?>
<?php snippet('footer') ?>