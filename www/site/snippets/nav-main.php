<nav class="navbar p-3">
    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php
        $items = $pages->filterBy('intendedTemplate', 'in', ['home', 'default', 'loadmore', 'legal']);
        foreach ($items as $item) :
        ?>
            <?php if ($item->hasChildren()) : ?>
                <li class="nav-item dropdown">
                    <?php if ($item->isActive()) : ?>
                        <a class="nav-link active template-<?= $item->intendedTemplate() ?>" aria-current="page" href="<?= $item->url() ?>" aria-haspopup="true" aria-expanded="false">
                        <?php else : ?>
                            <a class="nav-link template-<?= $item->intendedTemplate() ?>" href="<?= $item->url() ?>" aria-haspopup="true" aria-expanded="false">
                            <?php endif ?>
                            <?= $item->title()->html() ?>
                            </a>
                            <ul class="dropdown-menu">
                                <?php $itemchilds = $item->children();
                                foreach ($itemchilds as $ic) : ?>
                                    <li class="nav-item px-2 py-1">
                                        <?php if ($ic->isActive()) : ?>
                                            <a class="nav-link active template-<?= $ic->intendedTemplate() ?>" aria-current="page" href="<?= $ic->url() ?>">
                                            <?php else : ?>
                                                <a class="nav-link template-<?= $ic->intendedTemplate() ?>" href="<?= $ic->url() ?>">
                                                <?php endif ?>
                                                <?= $ic->title()->html() ?>
                                                </a>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                </li>

            <?php else : ?>
                <li class="nav-item">
                    <?php if ($item->isActive()) : ?>
                        <a class="nav-link active template-<?= $item->intendedTemplate() ?>" aria-current="page" href="<?= $item->url() ?>">
                        <?php else : ?>
                            <a class="nav-link template-<?= $item->intendedTemplate() ?>" href="<?= $item->url() ?>">
                            <?php endif ?>
                            <?= $item->title()->html() ?>
                            </a>
                </li>
            <?php endif ?>
        <?php endforeach ?>
    </ul>
    <button class="navigation-toggler hamburger btn-responsive" type="button" aria-expanded="false" aria-label="Toggle navigation">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
    </button>
    <?php if ($page->intendedTemplate() == 'home') : ?>
        <button class="filter__btn btn btn-primary btn-responsive" type="button" aria-label="Filter öffnen">
            <i icon-name="filter" class="nav-icon icon-only d-block"></i>
        </button>
    <?php endif ?>
</nav>