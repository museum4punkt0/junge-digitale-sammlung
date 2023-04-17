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

                    <div class="pe-none twitter-container single-exhibit <?= $model_size ?> <?= $exhibit_type_class ?>">
                        <?= $url->code() ?>
                        <a title="<?= $exhibit->title() ?>" class="exhibit-link cover-link" href="<?= $exhibit->url() ?>">
                            <?= $exhibit->title()->value() ?>
                        </a>
                    </div>

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