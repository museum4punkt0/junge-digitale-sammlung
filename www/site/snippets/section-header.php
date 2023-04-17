<header <?php if (isset($isFrontEnd)) : ?> class="frontend" <?php endif ?>>

    <div class="logos__container p-4 <?php if (!isset($isFrontEnd)) : ?> backend <?php endif ?>">
        <?php if ($image1 = $site->institutionlogo()->toFile()) : ?>
            <div class="logo__header logo__institution">
                <a class="navbar-brand d-block" href="<?= $site->url() ?>">
                    <img src="<?= $image1->url() ?>" data-lazyload alt="<?= $image1->alt() ?>">
                </a>
            </div>
        <?php endif ?>
        <?php if ($image2 = $site->projectlogo()->toFile()) : ?>
            <div class="logo__header logo__project">
                <a class="navbar-brand d-block" href="<?= $site->url() ?>">
                    <img src="<?= $image2->url() ?>" data-lazyload alt="<?= $image2->alt() ?>">
                </a>
            </div>
        <?php endif ?>
    </div>
    <?php if ($authenticated && !isset($isFrontEnd)) : ?>
        <?= snippet('nav-user_meta'); ?>
    <?php else : ?>
        <?= snippet('nav-main'); ?>
    <?php endif ?>
</header>