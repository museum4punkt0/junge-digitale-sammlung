<!-- if not collage, it is an exhibit podest -->
<?php if (!$isPosterCollage) : ?>
    <?php if ($exhibit_type_class == "embed") : ?>
        <!-- embeds -->
        <?php if ($url = $collectionItem->embed_url()->toEmbed()) : ?>
            <?php if ($url->providerName()->lower() == 'twitter') : ?>
                <!-- twitter -->
                <?php if (isFeatureAllowed('embeds')) : ?>
                    <div class="twitter-framer">
                        <div class="pe-none twitter-container single-exhibit <?= $compact_exhibit_class ?> <?= $exhibit_type_class ?>" tabindex="-1">
                            <?= $url->code() ?>
                            <div class="spinner-border text-primary fs-3" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <a title="<?= $collectionItem->title() ?>" class="exhibit-link cover-link" href="<?= $collectionItem->id() ?>">
                            <?= $collectionItem->title()->value() ?>
                        </a>
                    </div>
                <?php else : ?>
                    <div class="no-cookies">
                        <a title="<?= $collectionItem->title() ?>" class="" href="<?= $collectionItem->id() ?>">
                            <div class="exhibit-embed d-block h-100">
                                <p>
                                    <?= snippet('renderers/labeler', ['field' => 'cookies_infotext', 'fallback' => 'Cookies für externe Inhalte sind deaktiviert. Bitte passe die Einstellungen an, wenn du diese Inhalte sehen willst.']) ?>
                                </p>
                                <p>
                                    <i icon-name="cookie" class="icon-only"></i>
                                </p>
                            </div>
                        </a>
                    </div>
                <?php endif; ?>
            <?php elseif ($url->providerName()->lower() == 'tiktok' || $url->providerName()->lower() == 'instagram') : ?>
                <!-- dynamic insta and titktok -->
                <a title="<?= $collectionItem->title() ?>" class="exhibit-link single-exhibit <?= $compact_exhibit_class ?> <?= $exhibit_type_class ?>" href="<?= $collectionItem->id() ?>">
                    <div class="load-embed-img" href="<?= $url->url() ?>">
                        <div class="spinner-border text-primary fs-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </a>
            <?php else : ?>
                <!-- all others -->
                <a title="<?= $collectionItem->title() ?>" class="exhibit-link single-exhibit <?= $compact_exhibit_class ?> <?= $exhibit_type_class ?>" href="<?= $collectionItem->id() ?>">
                    <?php if ($url->image()) : ?>
                        <img src="<?= $url->image() ?>" alt="<?= $url->title() ?>">
                    <?php else : ?>
                        <p class="single-exhibit text-danger">Konnte nicht geladen werden<span class="empty"></span></p>
                    <?php endif ?>
                </a>
            <?php endif ?>

        <?php else : ?>
            <a title="<?= $collectionItem->title() ?>" class="exhibit-link single-exhibit <?= $compact_exhibit_class ?> <?= $exhibit_type_class ?>" href="<?= $collectionItem->id() ?>">
                <p class="single-exhibit text-danger">Konnte nicht geladen werden<span class="empty"></span></p>
            </a>
        <?php endif ?>
    <?php else : ?>
        <a title="<?= $collectionItem->title() ?>" class="exhibit-link single-exhibit <?= $compact_exhibit_class ?> <?= $exhibit_type_class ?>" href="<?= $collectionItem->id() ?>">
            <?php if ($img = $collectionItem->exhibit_preview()->toFile()) : ?>
                <?= $img->responsiveImg() ?>
            <?php else : ?>
                <p class="single-exhibit text-danger">Konnte nicht geladen werden<span class="empty"></span></p>
            <?php endif ?>
        </a>
    <?php endif ?>
<?php else : ?>
    <!-- poster collage -->
    <?php if ($exhibit_type_class == "embed") : ?>
        <?php if ($url = $collectionItem->embed_url()->toEmbed()) : ?>
            <?php if ($url->providerName()->lower() == 'twitter') : ?>
                <div class="embed__logo"><i icon-name="twitter" class="icon-only"></i></div>
            <?php elseif ($url->providerName()->lower() == 'tiktok' || $url->providerName()->lower() == 'instagram') : ?>
                <div class="load-embed-img" href="<?= $url->url() ?>">
                    <div class="spinner-border text-primary fs-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            <?php else : ?>
                <img src="<?= $url->image() ?>" alt="<?= $collectionItem->title() ?>">
            <?php endif ?>
        <?php else : ?>
            <div class="empty"><i icon-name="help-circle" class="icon-only"></i></div>
        <?php endif ?>
    <?php else : ?>
        <?php if ($img = $collectionItem->exhibit_preview()->toFile()) : ?>
            <img src="<?= $img->resize(100)->url(); ?>" alt="<?= $collectionItem->title() ?>" class="<?= $compact_exhibit_class ?>">
        <?php else : ?>
            <div class="empty"><i icon-name="help-circle" class="icon-only"></i></div>
        <?php endif ?>
    <?php endif ?>
<?php endif ?>