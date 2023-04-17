<?php

$participants = $page->parent()->childrenAndDrafts()->filterBy('intendedTemplate', 'c_curator')->sortBy('title');
$notDoneParticipants = $participants->filterBy('complete', 'false')->count();

$exhibits = $page->parent()->childrenAndDrafts()->filterBy('intendedTemplate', 'c_exhibit');
$doneExhibits = $exhibits->filterBy('complete', 'true')->count();
$notDoneExhibits = $notDoneParticipants - $doneExhibits;

$exhibitions = $page->parent()->childrenAndDrafts()->filterBy('intendedTemplate', 'c_exhibition');
$notDoneExhibitions = $exhibitions->filterBy('complete', 'false')->count();

$materialContainer = $site->children()->filterBy('intendedTemplate', 'materials')->first();
$materials = $page->parent()->materials()->toPages();

?>

<!-- Nav tabs -->
<div class="container tabs">
    <span page-slug="<?= $page->slug() ?>"></span>
    <nav>
        <ul class="nav nav-tabs" id="data-tabs" role="tablist">
            <li class="nav-item mx-lg-3" role="presentation">
                <button class="nav-link active" id="participants-tab" data-bs-toggle="tab" data-bs-target="#pane-participants" type="button" role="tab" aria-controls="pane-participants" aria-selected="true">
                    <span class="d-none d-lg-block">Teilnehmer</span>
                    <i icon-name="users" class="nav-icon d-block d-lg-none"></i>
                    <span class="status position-absolute top-0 start-100 translate-middle border border-light rounded-circle">
                        <span class="visually-hidden">Änderungen</span>
                    </span>
                </button>
            </li>
            <li class="nav-item mx-lg-3" role="presentation">
                <button class="nav-link" id="exhibitions-tab" data-bs-toggle="tab" data-bs-target="#pane-exhibitions" type="button" role="tab" aria-controls="pane-exhibitions" aria-selected="false">
                    <span class="d-none d-lg-block">Ausstellungen</span>
                    <i icon-name="layout-grid" class="nav-icon d-block d-lg-none"></i>
                    <span class="status position-absolute top-0 start-100 translate-middle border border-light rounded-circle">
                        <span class="visually-hidden">Änderungen</span>
                    </span>
                </button>
            </li>
            <li class="nav-item mx-lg-3" role="presentation">
                <button class="nav-link" id="participant-tab" data-bs-toggle="tab" data-bs-target="#pane-participant" type="button" role="tab" aria-controls="pane-participant" aria-selected="false">
                    <span class="d-none d-lg-block">Mein Profil</span>
                    <i icon-name="contact" class="nav-icon d-block d-lg-none"></i>
                    <span class="status position-absolute top-0 start-100 translate-middle border border-light rounded-circle">
                        <span class="visually-hidden">Änderungen</span>
                    </span>
                </button>
            </li>
            <?php if ($materials->isNotEmpty()) : ?>
                <li class="nav-item mx-lg-3" role="presentation">
                    <button class="nav-link" id="materials-tab" data-bs-toggle="tab" data-bs-target="#pane-materials" type="button" role="tab" aria-controls="pane-materials" aria-selected="false">
                        <span class="d-none d-lg-block">Materialien</span>
                        <i icon-name="files" class="nav-icon d-block d-lg-none"></i>
                    </button>
                </li>
            <?php endif ?>
        </ul>
    </nav>
</div>

<div class="tab-content">
    <!-- USERS -->
    <div id="pane-participants" class="tab-pane active container" role="tabpanel" aria-labelledby="participants-tab" tabindex="0">
        <h2>Teilnehmer</h2>
        <div class="participant__list">
            <div class="vstack border-bottom py-2 gap-3">
                <div class="hstack gap-3">
                    <div class="p-index meta help">#</div>
                    <div class="p-title stack__header">Name</div>
                    <div class="p-id stack__header">ID</div>
                    <div class="p-status stack__header excerpt">Daten</div>
                    <div class="o-status stack__header excerpt">Objekt</div>
                    <div class="o-link stack__header excerpt">Obj.<br>Name</div>
                    <div class="p-pin__reset stack__header excerpt">PIN Reset</div>
                </div>
            </div>
            <?php
            $index = 1;
            foreach ($participants as $participant) : ?>
                <?= snippet('forms_blocks/list_curators', ['participant' => $participant, 'index' => $index]) ?>
                <?php $index++ ?>
            <?php endforeach ?>
        </div>
    </div>
    <!-- EXHIBITIONS -->
    <div id="pane-exhibitions" class="tab-pane container" role="tabpanel" aria-labelledby="exhibitions-tab" tabindex="0">
        <?php
        $exhibitions = $page->parent()->childrenAndDrafts()->filterBy('intendedTemplate', 'c_exhibition')->sortBy('title'); ?>
        <h2>Ausstellungen</h2>

        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exhibitionModal">Ausstellung anlegen</button>
        <?= snippet('forms_blocks/modals/modal_exhibition') ?>

        <div class="exhibitions__list">
            <div class="vstack border-bottom py-2 gap-3">
                <div class="hstack gap-3">
                    <div class="p-index meta help">#</div>
                    <div class="p-title stack__header">Name</div>
                    <div class="o-count stack__header excerpt">Objekte</div>
                    <div class="u-count stack__header excerpt">Teiln.</div>
                    <div class="p-status stack__header excerpt">Daten</div>
                    <div class="e-exhibition__edit stack__header excerpt">Bearbeiten</div>
                    <div class="e-exhibition__delete stack__header excerpt">Löschen</div>
                </div>
            </div>
            <?= snippet('forms_blocks/list_exhibitions', ['exhibitions' => $exhibitions]) ?>
        </div>
    </div>
    <!-- LEADER -->
    <div id="pane-participant" data-pageid="<?= $page->uuid() ?>" class="tab-pane container" role="tabpanel" aria-labelledby="participant-tab" tabindex="0">
        <form id="user-form" class="user-form watchdog__form" action="<?= $page->url() ?>" method="POST" <?php if (isset($metainfo)) : ?> <?= $metainfo ?> <?php endif ?>>
            <div class="row">
                <h2>
                    Mein Profil
                </h2>
            </div>
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
            <div class="row mt-3">
                <div class="col-md-4 offset-md-6 col-lg-3 offset-lg-7">
                    <div class="vstack gap-2">
                        <button class="btn btn-primary save__user-button" type="submit" name="save-user" value="User speichern">
                            Speichern <i icon-name="save" class="float-end"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- MATERIALS -->
    <div id="pane-materials" data-pageid="<?= $page->uuid() ?>" class="tab-pane container" role="tabpanel" aria-labelledby="materials-tab" tabindex="0">
        <?php if ($materialContainer && $materials->isNotEmpty()) : ?>
            <h2><?= $materialContainer->title() ?></h2>
            <?= $materialContainer->introduction() ?>
        <?php endif ?>
        <?php if ($materials->isNotEmpty()) : ?>
            <?php foreach ($materials as $material_package) : ?>
                <div class="row">
                    <?php $files =  $material_package->files()->filterBy('extension', 'zip')->first(); ?>
                    <a download href="<?= $files->url() ?>">
                        <div class="hstack gap-3">
                            <p class="mb-0 pt-2"><?= $material_package->title() ?></p>
                            <i icon-name="download" class="nav-icon d-block"></i>
                        </div>
                    </a>
                </div>
            <?php endforeach ?>
        <?php endif ?>
    </div>
</div>
<div class="container my-3">
    <div class="row">
        <a class="btn btn-primary send-ws " data-bs-target="#sendWorkshopModal" data-bs-toggle="modal">
            Workshop abschicken
        </a>
    </div>
    <?= snippet('factories/legals') ?>
</div>

<?php snippet('forms_blocks/modals/modal_send_workshop', ['notDoneExhibits' => $notDoneExhibits, 'notDoneParticipants' => $notDoneParticipants, 'notDoneExhibitions' => $notDoneExhibitions]); ?>