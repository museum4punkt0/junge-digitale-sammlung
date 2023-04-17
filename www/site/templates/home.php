<?php snippet('header') ?>

<?php snippet('section-header', ['isFrontEnd' => true]) ?>

<?php
// in case data for population exists
if ($data = $site->data_populators_pick()->toPage()) {
    $options = $data->impulse()->toStructure();
    $startingImpulse = 0;
    //$startingImpulse = rand(0, ($options->count() - 1)); 
    $startingImpulse = $data->impulse()->yaml()[$startingImpulse]['id'];
    $states = $data->curator_state()->toStructure();
    $schoolclasses = $data->schoolclass()->toStructure();
} else {
    $options = [];
    $startingImpulse = 0;
    $startingImpulse = 0;
    $states = [];
    $schoolclasses = [];
}
?>

<main>
    <div id="wrapper">
        <div class="position-absolute w-100 h-100 top-0">
            <div class="scroll__area position-relative w-100 h-100">

                <div id="scroll__container" class="scroll__container position-relative">
                    <div class="filter__container">
                        <div class="overlay filter__overlay"></div>

                        <select class="select-collection" name="collection_selector" id="collection_selector">
                            <option value="c_exhibit" selected>Objekte</option>
                            <option value="c_exhibition">Ausstellungen</option>
                        </select>
                        <button type="button" class="x-btn btn d-inline-block d-md-none" aria-label="Filter schließen">
                            <i icon-name="x" class=""></i>
                        </button>

                        <select class="select-impulse" name="impulse_selector" id="impulse_selector">
                            <?php foreach ($options as $option) : ?>
                                <option <?= e($startingImpulse == $option->id(), 'selected') ?> value="<?= $option->id() ?>"> <?= $option->desc() ?></option>
                            <?php endforeach ?>
                        </select>
                        <div class="hstack position-relative gap-4">
                            <div class="dropdown">
                                <button type="button" class="btn btn-primary dropdown-toggle filter-toggle collapsed" data-bs-toggle="collapse" aria-expanded="false">
                                    Filter
                                    <span class="d-none position-absolute top-0 start-100 translate-middle badge border rounded-pill bg-primary">
                                        <span class="visual-information">0</span>
                                        <span class="visually-hidden">Filter-Menge</span>
                                    </span>
                                </button>
                                <div id="dd-filter" class="collapse dd-filter">
                                    <div class="flex-wrapper d-flex">
                                        <select class="select-curator_state filter__select" name="curator_state" id="curator_state_selector">
                                            <?php foreach ($states as $state) : ?>
                                                <option value="<?= $state->id() ?>"> <?= $state->desc() ?></option>
                                            <?php endforeach ?>
                                        </select>
                                        <select class="select-schoolclass filter__select" name="schoolclass" id="schoolclass_selector">
                                            <?php foreach ($schoolclasses as $schoolclass) : ?>
                                                <option value="<?= $schoolclass->id() ?>"> <?= $schoolclass->desc() ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div id="autocomplete" class="autocomplete input-wrapper inputSearch hstack gap-3">
                                <span class="d-none position-absolute top-0 start-100 translate-middle badge border rounded-pill bg-primary">
                                    <span class="visual-information">0</span>
                                    <span class="visually-hidden">Ergebnisse</span>
                                </span>

                                <label for="home__search" class=" col-form-label">Suche</label>
                                <input type="text" class="autocomplete-input form-control" />
                                <ul class="autocomplete-result-list results__search"></ul>
                                <div class="">
                                    <button id="btn__search_submit" type="button" class="btn__search btn btn-primary">></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="load-more-container position-absolute" data-limit="<?= $limit ?>" collection-type="<?= $currentCollection ?>">
                        <div class="podest-container justify-content-between align-items-end">
                        </div>
                        <div class="loadmore-trigger spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>

                    <div class="home-intro blocks">
                        <?= $page->introtext() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php if (isFeatureAllowed('embeds')) : ?>
    <?= js('https://platform.twitter.com/widgets.js');?>
<?php endif ?>

<?= js('/assets/js/vendor/virtual-select.js') ?>
<?php snippet('footer', ['isFrontEnd' => true]) ?>