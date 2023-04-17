<div class="exhibits-container">

    <?php if (isset($_page)) : //only if exhibition we populate existing users
    ?>
        <?php $relatedPages = $_page->parent()->childrenAndDrafts()->filterBy('intendedTemplate', 'c_curator'); ?>
        <?php for ($i = 1; $i <= 5; $i++) : ?>

            <?php
            $selectedPage = $_page->content()->get('user' . $i)->toPageOrDraft();
            if ((isset($curator) && $curator == $selectedPage) || !isset($curator))
                $disableField = false;
            else
                $disableField = true;
            ?>

            <div class="form-group row">
                <label class="col-md-3 col-form-label">Objekt <?= $i ?></label>
                <div class="col-md-7">
                    <div class="row">
                        <div class="col-9">
                            <?= snippet('renderers/input_element', ['forcedValue' => $selectedPage ? $selectedPage->id() : null, 'name' => 'user' . $i, 'type' => 'select', 'group' => 'impulse_' . $_page->slug(), 'selectPopulator' => populateFromCollection($relatedPages), 'ajaxHandler' => 'checkImpulseRelationshipsList', 'disabled' => isset($curator)]); ?>
                            <?= snippet('renderers/input_element', ['_page' => $_page ?? null, 'forcePageValues' => true, 'name' => 'exhibit' . $i . 'text', 'type' => 'textarea', 'disabled' => $disableField]); ?>
                        </div>
                        <div class="col-3">
                            <?php if ($selectedPage && $linked_exhibit = $selectedPage->linked_exhibit()->toPageOrDraft()) : ?>

                                <?php
                                $exhibit_type_class = '';
                                switch ($linked_exhibit->type()->value()) {
                                    case 0:
                                        $exhibit_type_class = "physical";
                                        break;
                                    case 1:
                                        $exhibit_type_class = "embed";
                                        break;
                                    case 2:
                                        $exhibit_type_class = "born-digital";
                                        break;
                                } ?>

                                <?php if ($exhibit_type_class == "embed") : ?>
                                    <?php if ($url = $linked_exhibit->embed_url()->toEmbed()) : ?>
                                        <?php if ($url->providerName()->lower() == 'twitter') : ?>
                                            <span class="text-secondary">
                                                TWEET:
                                                <?= $url->url() ?>
                                            </span>
                                        <?php else : ?>
                                            <?php if ($url->image()) : ?>
                                                <img src="<?= $url->image() ?>" alt="">
                                            <?php else : ?>
                                                <span class="empty text-secondary opacity-50"><i icon-name="help-circle" class="icon-only"></i></span>
                                            <?php endif ?>
                                        <?php endif ?>
                                    <?php else : ?>
                                        <span class="empty text-secondary opacity-50"><i icon-name="help-circle" class="icon-only"></i></span>
                                    <?php endif ?>
                                <?php else : ?>
                                    <?php if ($image = $linked_exhibit->exhibit_preview()->toFile()) : ?>
                                        <?= $image->responsiveImg() ?>
                                    <?php else : ?>
                                        <span class="empty text-secondary opacity-50"><i icon-name="help-circle" class="icon-only"></i></span>
                                    <?php endif ?>
                                <?php endif ?>
                            <?php else : ?>
                                <span class="empty text-secondary opacity-50"><i icon-name="help-circle" class="icon-only"></i></span>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <?= snippet('renderers/fields_metainfo', ['name' => 'user' . $i]) ?>
                </div>
            </div>
        <?php endfor ?>
    <?php else : ?>
        <?php $relatedPages = $page->parent()->childrenAndDrafts()->filterBy('intendedTemplate', 'c_curator'); ?>
        <?php for ($i = 1; $i <= 5; $i++) : ?>
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Objekt <?= $i ?></label>
                <div class="col-md-7">
                    <div class="row">
                        <div class="col-9">
                            <?= snippet('renderers/input_element', ['forcedValue' => null, 'name' => 'user' . $i, 'type' => 'select', 'group' => 'impulse_newpage', 'selectPopulator' => populateFromCollection($relatedPages)]); ?>
                            <?= snippet('renderers/input_element', ['_page' => null, 'forcePageValues' => true, 'name' => 'exhibit' . $i . 'text', 'type' => 'textarea']); ?>
                        </div>
                        <div class="col-3">
                            <p style="height: calc(100% - 1rem)">
                                <span class="h-100 w-100 empty bg-light"></span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <?= snippet('renderers/fields_metainfo', ['name' => 'user' . $i]) ?>
                </div>
            </div>
        <?php endfor ?>
    <?php endif ?>
</div>