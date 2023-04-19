<?php
$linked_exhibit = $page->linked_exhibit()->toPageOrDraft();
$linked_exhibition = $page->linked_exhibition()->toPageOrDraft();

if (!$linked_exhibit && isset($data['exhibitname'])) {
  $linked_exhibit = $page->parent()->findPageOrDraft(Str::slug($data['exhibitname']));
}
?>

<section class="">
  <!-- Nav tabs -->
  <div class="container tabs">
    <nav>
      <ul class="nav nav-tabs" id="data-tabs" role="tablist">
        <li class="nav-item mx-md-3" role="presentation">
          <button class="nav-link active" id="participant-tab" data-bs-toggle="tab" data-bs-target="#pane-participant" type="button" role="tab" aria-controls="pane-participant" aria-selected="true">
            <span class="d-none d-md-block">Mein Profil</span>
            <i icon-name="contact" class="nav-icon d-block d-md-none"></i>
            <span class="status position-absolute top-0 start-100 translate-middle border border-light rounded-circle">
              <span class="visually-hidden">Änderungen</span>
            </span>
          </button>
        </li>
        <li class="nav-item mx-md-3" role="presentation">
          <button class="nav-link" id="exhibit-tab" data-bs-toggle="tab" data-bs-target="#pane-exhibit" type="button" role="tab" aria-controls="pane-exhibit" aria-selected="false">
            <span class="d-none d-md-block">Objekt</span>
            <i icon-name="box" class="nav-icon d-block d-md-none"></i>
            <span class="status position-absolute top-0 start-100 translate-middle border border-light rounded-circle">
              <span class="visually-hidden">Änderungen</span>
            </span>
          </button>
        </li>
        <?php if ($linked_exhibition) : ?>
          <li class="nav-item mx-md-3" role="presentation">
            <button class="nav-link" id="exhibition-tab" data-bs-toggle="tab" data-bs-target="#pane-exhibition" type="button" role="tab" aria-controls="pane-exhibition" aria-selected="false">
              <span class="d-none d-md-block">Ausstellung</span>
              <i icon-name="layout-grid" class="nav-icon d-block d-md-none"></i>
              <span class="status position-absolute top-0 start-100 translate-middle border border-light rounded-circle">
                <span class="visually-hidden">Änderungen</span>
              </span>
            </button>
          </li>
        <?php endif ?>
      </ul>
    </nav>
  </div>

  <div class="tab-content">
    <!-- Curator -->
    <div id="pane-participant" data-pageid="<?= $page->uuid() ?>" class="tab-pane active container" role="tabpanel" aria-labelledby="participant-tab" tabindex="0">
      <form id="user-form" class="user-form watchdog__form" action="<?= $page->url() ?>" method="POST" <?php if (isset($metainfo)) : ?> <?= $metainfo ?> <?php endif ?>>
        <div class="row">
          <h2>
            Mein Profil
          </h2>
        </div>
        <?= snippet('forms_blocks/fields/fields_curator') ?>
        <div class="row mt-3">
          <div class="col-md-4 offset-md-6 col-lg-3 offset-lg-7">
            <div class="vstack gap-2">
              <button class="btn btn-primary mobile-fixed mobile-right save__user-button" type="submit" name="save-user" value="User speichern">
                Speichern <i icon-name="save" class="float-end"></i>
              </button>
              <button type="button" class="btn btn-secondary mobile-fixed mobile-left btn-reset" data-bs-toggle="modal" data-bs-target="#confirmationModal" data-bs-headline="Formular zurücksetzen" data-bs-message="Sind Sie sicher, dass Sie das Formular zurücksetzen wollen?" data-bs-func="resetFormModal" data-bs-data="">
                Zurücksetzen <i icon-name="undo-2" class="float-end"></i>
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
    <!-- Exhibit -->
    <div id="pane-exhibit" data-pageid="<?= $linked_exhibit ? $linked_exhibit->uuid() : 'no-exhibit' ?>" class="tab-pane container" role="tabpanel" aria-labelledby="exhibit-tab" tabindex="0">
      <div class="row">
        <h2>
          Mein Objekt
        </h2>
      </div>
      <?php if ($linked_exhibit) : ?>
        <div class="form-group row mb-4">
          <!-- <label class="col-md-3 col-form-label" for="">Objekt</label> -->
          <div class="col-md-7 offset-md-3">
            <div class="hstack gap-4 align-items-end ">
              <?php if ($linked_exhibit->type()->value() == 0) : ?>
                <!-- PHYSICAL EXHIBIT -->
                <?= snippet('forms_blocks/fields/fields_exhibit-physical', ['page' => $page, 'linked_exhibit' => $linked_exhibit]) ?>
              <?php elseif ($linked_exhibit->type()->value() == 1) : ?>
                <!-- EMBED EXHIBIT -->
                <?= snippet('forms_blocks/fields/fields_exhibit-embed', ['page' => $page, 'linked_exhibit' => $linked_exhibit]) ?>
              <?php elseif ($linked_exhibit->type()->value() == 2) : ?>
                <!-- DIGITAL BORN EXHIBIT -->
                <?= snippet('forms_blocks/fields/fields_exhibit-digital', ['page' => $page, 'linked_exhibit' => $linked_exhibit]) ?>
              <?php endif ?>
            </div>
          </div>

          <div class="col-md-2">
            <?php if ($linked_exhibit->type()->value() == 0) : ?>
              <?= snippet('renderers/fields_metainfo', ['name' => 'museum_preview', 'composed' => ['museum_preview'], 'context' => $linked_exhibit]) ?>
            <?php elseif ($linked_exhibit->type()->value() == 1) : ?>
              <!-- entfällt, da man keine bilder/kopien auf dem eigenen server speichern darf -->
            <?php elseif ($linked_exhibit->type()->value() == 2) : ?>
              <?= snippet('renderers/fields_metainfo', ['name' => 'borndigital_files', 'composed' => ['exhibit_preview', 'digital_asset'], 'context' => $linked_exhibit]) ?>
            <?php endif ?>
          </div>
        </div>


        <hr>

        <!-- EXHIBIT FORM -->
        <form id="exhibit-form" class="exhibit-form watchdog__form" action="<?= $page->url() ?>" method="POST">
          <?php if ($linked_exhibit->type()->value() == 0) : ?>
            <div class="form-group row">
              <label for="exhibit_preview" class="col-md-3 col-form-label is-required">
                <?= snippet('renderers/labeler', ['field' => 'exhibit_3dpreview_label', 'fallback' => 'Vorschaubild des Objekts']) ?>
              </label>
              <div class="col-md-7">
                <?= snippet('renderers/input_element', ['_page' => $linked_exhibit, 'name' => 'exhibit_preview', 'type' => 'radioimages', 'ajaxHandler' => 'checkRadioGroupGallery']); ?>
              </div>
              <div class="col-md-2">
                <?= snippet('renderers/fields_metainfo', ['name' => 'exhibit_preview']) ?>
              </div>
            </div>
          <?php endif ?>

          <?= snippet('forms_blocks/fields/fields_exhibit', ['linkedpage' => $linked_exhibit]) ?>
          <div class="row mt-3">
            <div class="col-md-4 offset-md-6 col-lg-3 offset-lg-7">
              <div class="vstack gap-2">
                <a href="<?= $linked_exhibit->parent()->url() ?>/<?= $linked_exhibit->slug() ?>" target="_blank" class="btn btn-primary preview__exhibit-button">
                  Vorschau <i icon-name="eye" class="float-end"></i>
                </a>
                <button class="btn btn-primary mobile-fixed mobile-right save__exhibit-button" type="submit" name="save-exhibit" value="Objekt speichern">
                  Speichern <i icon-name="save" class="float-end"></i>
                </button>
                <button type="button" class="btn btn-secondary mobile-fixed mobile-left btn-reset" data-bs-toggle="modal" data-bs-target="#confirmationModal" data-bs-headline="Formular zurücksetzen" data-bs-message="Sind Sie sicher, dass Sie das Formular zurücksetzen wollen?" data-bs-func="resetFormModal" data-bs-data="">
                  Zurücksetzen <i icon-name="undo-2" class="float-end"></i>
                </button>
              </div>
            </div>
          </div>
        </form>

        <form id="exhibit-delete-form" class="exhibit-delete-form mt-2" action="<?= $page->url() ?>" method="POST">
          <div class="row">
            <div class="col-md-4 offset-md-6 col-lg-3 offset-lg-7">
              <div class="vstack gap-2">
                <input type="hidden" name="delete-exhibit" value="true">
                <button class="btn btn-secondary delete__exhibit-button" type="button" data-bs-toggle="modal" data-bs-target="#confirmationModal" data-bs-headline="Objekt löschen" data-bs-message="Sind Sie sicher, dass Sie das Objekt löschen wollen?" data-bs-func="sendAjaxForm" data-bs-data="exhibit-delete-form">
                  Löschen <i icon-name="trash-2" class="float-end"></i>
                </button>
              </div>
            </div>
          </div>
        </form>

      <?php else : ?>
        <div class="row">
          <div class="col">
            Bitte Objekt anlegen
            <form class="exhibit-create-form watchdog__form" action="<?= $page->url() ?>" method="POST">
              <div class="row">
                <div class="col">
                  <div class="form-group row">
                    <label for="exhibitname" class="col-md-3 col-form-label">
                      Objekt Name
                    </label>
                    <div class="col-md-7">
                      <?= snippet('renderers/input_element', ['name' => 'exhibitname', 'forcedValue' => $data['exhibitname'] ?? '', 'type' => 'text']); ?>
                    </div>
                    <div class="col-md-2">
                      <?= snippet('renderers/fields_metainfo', ['name' => 'exhibitname']) ?>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="type" class="col-md-3 col-form-label">
                      Objekttyp
                    </label>
                    <div class="col-md-7">
                      <?= snippet('renderers/input_element', ['forcedValue' => $data['type'] ?? '', 'name' => 'type', 'type' => 'select']); ?>
                    </div>
                    <div class="col-md-2">
                      <?= snippet('renderers/fields_metainfo', ['name' => 'type']) ?>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <button class="btn btn-primary save__exhibit-button submit" type="submit" name="create-exhibit" value="Objekt anlegen">
                  Objekt anlegen <i icon-name="save" class="float-end"></i>
                </button>
              </div>
            </form>

          </div>
        </div>
      <?php endif ?>
    </div>

    <!-- Exhibition -->

    <?php if ($linked_exhibition) : ?>

      <div id="pane-exhibition" data-pageid="<?= $linked_exhibition ? $linked_exhibition->uuid() : 'no-exhibition' ?>" class="tab-pane container" role="tabpanel" aria-labelledby="exhibition-tab" tabindex="0">

        <form id="exhibition-form" class="exhibition-form dynamic-content watchdog__form" action="<?= $page->url() ?>" method="POST">
          <div class="row">
            <h2>
              Ausstellung
            </h2>
          </div>
          <?= snippet('forms_blocks/fields/fields_exhibition', ['exhibition' => $linked_exhibition, 'curator' => $page]) ?>
          <input type="hidden" name="exhibition-id" id="exhibition-id" value="<?= $linked_exhibition->slug() ?>">
          <div class="row">
            <div class="col-md-4 offset-md-6 col-lg-3 offset-lg-7">
              <div class="vstack gap-2">
                <a href="<?= $linked_exhibition->parent()->url() ?>/<?= $linked_exhibition->slug() ?>" target="_blank" class="btn btn-primary preview__exhibit-button">
                  Vorschau <i icon-name="eye" class="float-end"></i>
                </a>
                <button class="btn btn-primary mobile-fixed mobile-right save__exhibition-button" type="submit" name="save-exhibition" value="Ausstellung speichern">
                  Speichern <i icon-name="save" class="float-end"></i>
                </button>
                <button type="button" class="btn btn-secondary mobile-fixed mobile-left btn-reset" data-bs-toggle="modal" data-bs-target="#confirmationModal" data-bs-headline="Formular zurücksetzen" data-bs-message="Sind Sie sicher, dass Sie das Formular zurücksetzen wollen?" data-bs-func="resetFormModal" data-bs-data="">
                  Zurücksetzen <i icon-name="undo-2" class="float-end"></i>
                </button>
              </div>
            </div>
          </div>
        </form>

        <div class="overlay_container"></div>
      </div>

    <?php endif ?>

    <?= snippet('factories/legals') ?>
  </div>

</section>