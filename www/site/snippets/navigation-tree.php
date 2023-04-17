<header class="navbar p-4">
  
    <!-- BRANDING -->
    <div class="navbar-section">
      <?php if ($image = $site->logo()->toFile()) : ?>
        <a class="navbar-brand link-route regular-logo" href="<?= $site->url() ?>">
          <img src="<?= $image->url() ?>" data-lazyload alt="<?= $image->alt() ?>">
        </a>
      <?php endif ?>
      <?php if ($image = $site->smalllogo()->toFile()) : ?>
        <a class="navbar-brand link-route small-logo" href="<?= $site->url() ?>">
          <img src="<?= $image->url() ?>" data-lazyload alt="<?= $image->alt() ?>">
        </a>
      <?php endif ?>
    </div>

    <!-- MAIN NAV -->
    <div class="navbar-section">
      <nav class="navbar">
        <ul class="menu__container">
          
          <?php
          // mobile detail
          if ($image = $site->mobnavbg()->toFile()) : ?>
            <img class="mob-nav-bg" src="<?= $image->url() ?>" alt="<?= $image->alt() ?>">
          <?php endif ?>
          <?php
          // nested menu
          // only show the menu if items are available
          $items = $pages->filterBy('intendedTemplate', 'in', ['home','default','c_workshop', 'loadmore']);

          if ($items->isNotEmpty()) :
          ?>
            <?php foreach ($items as $item) : ?>
              <?php if ($item->children()->listed()->isNotEmpty()) : ?>
                <li class="has-children"><a class="link-route <?php e($item->isActive(), "active") ?> <?php e($item->children()->findOpen(), "active") ?>" href="<?= $item->url() ?>"><?= $item->title()->html() ?><i class="icon icon-arrow-down"></i></a>
                  <?php
                  // get all children for the current menu item
                  $children = $item->children()->listed();
                  // display the submenu if children are available
                  if ($children->isNotEmpty()) :
                  ?>
                    <ul class="sub-menu">
                      <?php foreach ($children as $child) : ?>
                        <li class=" <?php e($child->isActive(), "active") ?>"><a class="link-route" href="<?= $child->url() ?>"><?= $child->title()->html() ?></a></li>
                      <?php endforeach ?>
                    </ul>
                  <?php endif ?>
                </li>
              <?php else : ?>
                <li class=" <?php e($item->isActive(), "active") ?>"><a class="link-route" href="<?= $item->url() ?>"><?= $item->title()->html() ?></a></li>
              <?php endif ?>
            <?php endforeach ?>
          <?php endif ?>
          <!-- LANG NAV -->
          <div>
            <nav class="navbar">
              <ul class="languages">
                <?php foreach ($kirby->languages() as $language) : ?>
                  <li class=" <?php e($kirby->language() == $language, 'active') ?>">
                    <a class="link-route" href="<?= $page->url($language->code()) ?>" hreflang="<?php echo $language->code() ?>">
                      <?= Str::lower(html($language->name())) ?>
                    </a>
                  </li>
                <?php endforeach ?>
              </ul>
            </nav>
          </div>
        </ul>
        <div class="hamburger">
          <span class="bar"></span>
          <span class="bar"></span>
          <span class="bar"></span>
        </div>
      </nav>
    </div>
  
</header>