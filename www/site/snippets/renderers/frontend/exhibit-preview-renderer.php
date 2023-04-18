<?php if ($user) : ?>
    <?php if ($exhibit = $user->linked_exhibit()->toPageOrDraft()) : ?>
        <?php
        $exhibit_type_class = '';
        switch ($exhibit->type()->value()) {
            case 0:
                $exhibit_type_class = "physical";
                break;
            case 1:
                $exhibit_type_class = "embed";
                break;
            case 2:
                $exhibit_type_class = "born-digital";
                break;
        }

        $model_size = $exhibit->threed_model_size()->toBool() ? 'size-compact' : 'size-regular';
        ?>

        <?php if ($exhibit_type_class == "embed") : ?>
            <?php if ($url = $exhibit->embed_url()->toEmbed()) : ?>
                <?php if ($url->providerName()->lower() == 'twitter') : ?>
                    <?php if (isFeatureAllowed('embeds')) : ?>
                        <div class="pe-none twitter-container single-exhibit <?= $model_size ?> <?= $exhibit_type_class ?>">
                            <?= $url->code() ?>
                            <div class="spinner-border text-primary fs-3" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <a title="<?= $exhibit->title() ?>" class="exhibit-link cover-link" href="<?= $exhibit->url() ?>">
                                <?= $exhibit->title()->value() ?>
                            </a>
                        </div>
                    <?php else : ?>
                        <div class="no-cookies">
                            <a title="<?= $exhibit->title() ?>" class="" href="<?= $exhibit->url() ?>">
                                <div class="exhibit-embed d-block h-100">
                                    <p>
                                        <?= snippet('renderers/labeler', ['field' => 'cookies_infotext', 'fallback' => 'Cookies für externe Inhalte sind deaktiviert. Bitte passe die Einstellungen an, wenn du diese Inhalte sehen willst.']) ?>
                                    <div id="edit-embed-cookie"><strong role="button">Anpassen</strong></div>
                                    <script>
                                        document.querySelector('#edit-embed-cookie').addEventListener('click', function(e) {
                                            e.stopPropagation();
                                            e.preventDefault();
                                            const event = document.createEvent('HTMLEvents');
                                            event.initEvent('cookies:update', true, false);
                                            document.querySelector('body').dispatchEvent(event);
                                        });
                                    </script>
                                    </p>
                                    <p>
                                        <i icon-name="cookie" class="icon-only"></i>
                                    </p>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>

                <?php elseif ($url->providerName()->lower() == 'tiktok' || $url->providerName()->lower() == 'instagram') : ?>
                    <a title="<?= $exhibit->title() ?>" class="exhibit-link single-exhibit <?= $model_size ?> <?= $exhibit_type_class ?>" href="<?= $exhibit->url() ?>">
                        <div class="load-embed-img" href="<?= $url->url() ?>">
                            <div class="spinner-border text-primary fs-3" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </a>
                <?php else : ?>
                    <a title="<?= $exhibit->title() ?>" class="exhibit-link single-exhibit <?= $model_size ?> <?= $exhibit_type_class ?>" href="<?= $exhibit->url() ?>">
                        <?php if ($url->image()) : ?>
                            <img src="<?= $url->image() ?>" alt="">
                        <?php else : ?>
                            <p class="single-exhibit">
                                <span class="empty"><i icon-name="help-circle" class="icon-only"></i></span>
                            </p>
                        <?php endif ?>
                    </a>
                <?php endif ?>
            <?php else : ?>
                <a title="<?= $exhibit->title() ?>" class="exhibit-link single-exhibit <?= $model_size ?> <?= $exhibit_type_class ?>" href="<?= $exhibit->url() ?>">
                    <div class="single-exhibit">
                        <span class="empty"><i icon-name="help-circle" class="icon-only"></i></span>
                    </div>
                </a>
            <?php endif ?>
        <?php else : ?>
            <a title="<?= $exhibit->title() ?>" class="exhibit-link single-exhibit <?= $model_size ?> <?= $exhibit_type_class ?>" href="<?= $exhibit->url() ?>">
                <?php if ($image = $exhibit->exhibit_preview()->toFile()) : ?>
                    <?= $image->responsiveImg() ?>
                <?php else : ?>
                    <div class="single-exhibit">
                        <span class="empty"><i icon-name="help-circle" class="icon-only"></i></span>
                    </div>
                <?php endif ?>
            </a>
        <?php endif ?>

    <?php endif ?>
<?php endif ?>